<?php declare(strict_types=1);
namespace PHPWell\PHPGenerator\Entity;

/**
 * Class Project
 * @package PHPWell\PHPGenerator
 */
class Project
{
    /** @var string */
    private $name;

    /** @var ProjectType */
    private $type;

    /** @var string|null */
    private $namespace;

    /** @var array */
    private $config;

    /**
     * Project constructor.
     * @param string $name
     * @param ProjectType $type
     * @param string|null $namespace
     */
    public function __construct(string $name, ProjectType $type, ?string $namespace)
    {
        $this->name = $name;
        $this->type = $type;
        $this->namespace = $namespace;
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
     * @return ProjectType
     */
    public function getType(): ProjectType
    {
        return $this->type;
    }

    /**
     * @param ProjectType $type
     */
    public function setType(ProjectType $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string|null
     */
    public function getNamespace(): ?string
    {
        return $this->namespace;
    }

    /**
     * @return string|null
     */
    public function getNamespaceFormatted(): ?string
    {
        return sprintf('namespace %s;', $this->namespace);
    }

    /**
     * @param string|null $namespace
     */
    public function setNamespace(?string $namespace): void
    {
        $this->namespace = $namespace;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * @param string $config
     */
    public function addConfig(string $config): void
    {
        $this->config[] = $config;
    }
}