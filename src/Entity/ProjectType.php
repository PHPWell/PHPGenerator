<?php declare(strict_types=1);
namespace PHPWell\PHPGenerator\Entity;

/**
 * Class ProjectType
 * @package PHPWell\PHPGenerator
 */
class ProjectType
{
    /** @var string */
    private $name;

    /** @var string|null */
    private $detail;

    /** @var Blueprint[] */
    private $blueprints;

    /**
     * ProjectType constructor.
     * @param string $name
     * @param string|null $detail
     * @param Blueprint[] $blueprints
     */
    public function __construct(string $name, ?string $detail, array $blueprints)
    {
        $this->name = $name;
        $this->detail = $detail;
        $this->blueprints = $blueprints;
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
     * @return string|null
     */
    public function getDetail(): ?string
    {
        return $this->detail;
    }

    /**
     * @param string|null $detail
     */
    public function setDetail(?string $detail): void
    {
        $this->detail = $detail;
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
    public function setBlueprints(array $blueprints): void
    {
        $this->blueprints = $blueprints;
    }
}