<?php declare(strict_types=1);
namespace PHPWell\PHPGenerator;

use Exception;
use Nette\Utils\FileSystem;
use Nette\Neon\Neon;
use PHPWell\PHPGenerator\Entity\Blueprint;
use PHPWell\PHPGenerator\Entity\Project;
use PHPWell\PHPGenerator\Entity\ProjectType;
use PHPWell\PHPGenerator\Entity\File;

/**
 * Class PHPGenerator
 * @package PHPWell\PHPGenerator
 */
class PHPGenerator
{
    /** @var array */
    private $configuration;

    /** @var string */
    private $blueprintsPath;

    /** @var string */
    private $outputPath;

    /** @var ProjectType[] */
    private $projectTypes;

    /** @var Blueprint[] */
    private $blueprints;

    /** @var Project */
    private $project;

    /**
     * @param string $configurationPath
     * @return $this
     * @throws Exception
     */
    public function loadConfiguration(string $configurationPath): PHPGenerator
    {
        if(!file_exists($configurationPath)) {
            throw new Exception('No configuration found');
        }

        $this->configuration = Neon::decode(FileSystem::read($configurationPath));
        return $this;
    }

    /**
     * @param string $blueprintDirectoryPath
     * @return $this
     * @throws Exception
     */
    public function setBlueprintsPath(string $blueprintDirectoryPath): PHPGenerator
    {
        if(!file_exists($blueprintDirectoryPath)) {
            throw new Exception('No blueprint direcory found');
        }

        $this->blueprintsPath = $blueprintDirectoryPath;
        return $this;
    }

    /**
     * @param string $outputDirectoryPath
     * @return $this
     * @throws Exception
     */
    public function setOutputDirectory(string $outputDirectoryPath): PHPGenerator
    {
        if(!file_exists($outputDirectoryPath)) {
            throw new Exception('No output direcory found');
        }

        $this->outputPath = $outputDirectoryPath;
        return $this;
    }

    /**
     * @return $this
     */
    public function init(): PHPGenerator
    {
        foreach($this->configuration['blueprints'] as $blueprintName => $blueprint) {

            $this->addBlueprint(
                new Blueprint(
                    $blueprintName
                    , new File(
                        $blueprint['ext']
                        , $blueprint['prefix']           ??  null
                        , $blueprint['suffix']           ??  null
                    )
                    , $blueprint['replacements']    ??  []
                    , $blueprint['cfg']             ??  []
                )
            );
        }

        foreach($this->configuration['types'] as $projectTypeName => $projectType) {

            $projectBlueprints = [];
            foreach($this->getBlueprints() as $blueprint) {
                if(in_array($blueprint->getName(), $projectType['blueprints'], true)) {
                    $projectBlueprints[] = $blueprint;
                }
            }

            $this->addProjectType(
                new ProjectType(
                   $projectTypeName
                    , $projectType['detail']
                    , $projectBlueprints
                )
            );
        }

        $this->setProject(
            $this->generateProject()
        );

        $this->generate($this->getProject());
        $this->generateConfig();

        return $this;
    }

    /**
     * @return Project
     */
    private function generateProject(): Project
    {
        $project = null;
        $type = null;
        $namespace = null;

        $this->p('----------------------------------------------');
        $this->p('PHPGenerator init..');
        $this->p('----------------------------------------------');
        while(empty($project)) {
            $this->p();
            $this->p('1) Type name of the project (required): ');
            $project = $this->getConsoleInput();
        }
        $this->p();
        $this->p('Project name set to: "'.$project.'"');
        $this->p();
        $this->p('----------------------------------------------');
        while(empty($type)) {
            $this->p();
            $this->p('2) Project types:');
            $this->p('| KEY | Value');
            foreach($this->getProjectTypes() as $projectType) {
                $this->p('| '.$projectType->getName().' | '.$projectType->getDetail());
            }
            $this->p('2.1) Select project type (required):');
            $type = $this->getConsoleInput();
            if(!array_key_exists($type, $this->getProjectTypes())) {
                $type = null;
            }
        }
        $this->p();
        $this->p('Project type set to: "' . $type . '"');
        $this->p();
        $this->p('----------------------------------------------');
        $this->p();
        $this->p('3) Type namespace of the project: [leave blank if you dont want namespace]');
        $namespace = $this->getConsoleInput();
        if(empty($namespace)) {
            $namespace = null;
        }
        $this->p();
        $this->p('Namespace set to: "'.$namespace.'"');
        $this->p();
        $this->p('----------------------------------------------');
        $this->p('Configuration done.');
        $this->p('Generating into "'.$this->getOutputPath().'"');
//////////////////////////////////////////////////////////////////////////////////////////////////////
        $this->p('----------------------------------------------');
//////////////////////////////////////////////////////////////////////////////////////////////////////
        $projectTypeEntity = null;
        foreach($this->getProjectTypes() as $projectType) {
            if($projectType->getName() === $type) {
                $projectTypeEntity = $projectType;
            }
        }
        return new Project($project, $projectTypeEntity, $namespace);
    }

    /**
     *
     */
    private function generateConfig(): void
    {
        $configuration = $this->getProject()->getConfig();
        if(count($configuration) > 0) {

            $this->p('----------------------------------------------');
            $this->p('Insert next lines into your configuration file');
            $this->p('----------------------------------------------');

            foreach($this->getProject()->getConfig() as $cfg) {
                $this->p($cfg);
            }

            $this->p('----------------------------------------------');
        }

    }

    /**
     * @param Project $project
     */
    private function generate(Project $project): void
    {
        $projectPath = sprintf('%s%s', $this->getOutputPath(), $project->getName());
        if(file_exists($projectPath)) {
            $this->p('Generating stopped! Project already generated!');
            return;
        }

        FileSystem::createDir($projectPath);
        $blueprints = $project->getType()->getBlueprints();

        foreach($blueprints as $blueprint) {
            $filePath = $this->copyBlueprint($project, $blueprint, $projectPath);
            $args = $this->prepareArgs($blueprint->getReplacements(), $project, $blueprint, $filePath);
            $this->renderTemplate($filePath, $args);

            foreach($blueprint->getConfiguration() as $cfg) {
                $project->addConfig($this->applyParams($cfg, $args));
            }
        }
    }

    /**
     * @param array $args
     * @param Project $project
     * @param Blueprint $blueprint
     * @param string $filePath
     * @return array
     */
    private function prepareArgs(array $args, Project $project, Blueprint $blueprint, string $filePath): array
    {
        $return = [];
        foreach($args as $arg) {
            switch($arg) {
                case 'project':
                    $return['%%%project%%%'] = $project->getName();
                    break;
                case 'namespace_only':
                    $return['%%%namespace_only%%%'] = $project->getNamespace();
                    break;
                case 'namespace':
                    $return['%%%namespace%%%'] = $project->getNamespaceFormatted();
                    break;
                case 'loweredProjectName':
                    $return['%%%loweredProjectName%%%'] = lcfirst($project->getName());
                    break;
                case 'prefix':
                    $return['%%%prefix%%%'] = $blueprint->getFile()->getPrefix() ?? null;
                    break;
                case 'suffix':
                    $return['%%%suffix%%%'] = $blueprint->getFile()->getSuffix() ?? null;
                    break;
                case 'file':
                    $explode1 = explode(DIRECTORY_SEPARATOR, $filePath);
                    $fileName = $explode1[count($explode1) - 1];
                    $return['%%%file%%%'] = $fileName;
                    break;
                case 'class':
                    $explode1 = explode(DIRECTORY_SEPARATOR, $filePath);
                    $fileName = $explode1[count($explode1) - 1];
                    $explode2 = explode('.', $fileName);
                    $className = $explode2[count($explode2) - 2];
                    $return['%%%class%%%'] = $className;
                    break;
                default: break;
            }
        }

        return $return;
    }

    /**
     * @param Project $project
     * @param Blueprint $blueprint
     * @param string $projectPath
     * @return string
     */
    private function copyBlueprint(Project $project, Blueprint $blueprint, string $projectPath): string
    {
        $filename = sprintf(
            '%s%s%s.%s'
            , $blueprint->getFile()->getPrefix()     ?? null
            , $project->getName()
            , $blueprint->getFile()->getSuffix()     ?? null
            , $blueprint->getFile()->getExtension()  ?? null
        );

        $filePath = sprintf('%s/%s', $projectPath, $filename);
        FileSystem::copy(
            sprintf('%s%s.%s', $this->getBlueprintsPath(), $blueprint->getName(), Constant::blueprint_ext),
            $filePath,
            true
        );

        $this->p("Applying blueprint: " . $blueprint->getName());
        return $filePath;
    }

    /**
     * @param string $file
     * @param array $params
     */
    private function renderTemplate(string $file, array $params): void
    {
        $fileString = file_get_contents($file);
        $fileString = $this->applyParams($fileString, $params);
        file_put_contents($file, $fileString);
        $this->p("Generating: " . $file);
    }

    /**
     * @param string $string
     * @param array $params
     * @return string
     */
    private function applyParams(string $string, array $params): string
    {
        return str_replace(array_keys($params), array_values($params), $string);
    }

    /**
     * @param string|null $text
     */
    private function p(?string $text = null): void
    {
        print $text.PHP_EOL;
    }

    /**
     * @return string
     */
    private function getConsoleInput(): string
    {
        $handle = fopen ("php://stdin","r");
        $line = fgets($handle);
        fclose($handle);
        return trim($line);
    }

    /**
     * @return Blueprint[]
     */
    public function getBlueprints(): array
    {
        return $this->blueprints;
    }

    /**
     * @param Blueprint[] $blueprints
     */
    private function setBlueprints(array $blueprints): void
    {
        $this->blueprints = $blueprints;
    }

    /**
     * @param Blueprint $blueprint
     */
    private function addBlueprint(Blueprint $blueprint): void
    {
        $this->blueprints[$blueprint->getName()] = $blueprint;
    }

    /**
     * @return ProjectType[]
     */
    public function getProjectTypes(): array
    {
        return $this->projectTypes;
    }

    /**
     * @param ProjectType[] $projectTypes
     */
    private function setProjectTypes(array $projectTypes): void
    {
        $this->projectTypes = $projectTypes;
    }

    /**
     * @param ProjectType $projectType
     */
    private function addProjectType(ProjectType $projectType): void
    {
        $this->projectTypes[$projectType->getName()] = $projectType;
    }

    /**
     * @return Project
     */
    public function getProject(): Project
    {
        return $this->project;
    }

    /**
     * @param Project $project
     */
    private function setProject(Project $project): void
    {
        $this->project = $project;
    }

    /**
     * @return string
     */
    public function getBlueprintsPath(): string
    {
        return $this->blueprintsPath;
    }

    /**
     * @return string
     */
    public function getOutputPath(): string
    {
        return $this->outputPath;
    }
}