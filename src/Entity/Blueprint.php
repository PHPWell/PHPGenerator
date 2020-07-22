<?php declare(strict_types=1);
namespace PHPWell\PHPGenerator\Entity;

/**
 * Class Blueprint
 * @package PHPWell\PHPGenerator
 */
class Blueprint
{
    /** @var string */
    private $name;

    /** @var File */
    private $file;

    /** @var array */
    private $replacements;

    /** @var array */
    private $configuration;

    /**
     * Blueprint constructor.
     * @param string $name
     * @param File $file
     * @param array $replacements
     * @param array $configuration
     */
    public function __construct(string $name, File $file, array $replacements, array $configuration)
    {
        $this->name = $name;
        $this->file = $file;
        $this->replacements = $replacements;
        $this->configuration = $configuration;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }

    /**
     * @param File $file
     */
    public function setFile(File $file): void
    {
        $this->file = $file;
    }

    /**
     * @return Replacement[]
     */
    public function getReplacements(): array
    {
        return $this->replacements;
    }

    /**
     * @param Replacement[] $replacements
     */
    public function setReplacements(array $replacements): void
    {
        $this->replacements = $replacements;
    }

    /**
     * @return array
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    /**
     * @param array $configuration
     */
    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }
}