<?php declare(strict_types=1);
namespace PHPWell\PHPGenerator;

/**
 * Class File
 * @package PHPWell\PHPGenerator
 */
class File
{
    /** @var string|null */
    private $prefix;

    /** @var string|null */
    private $suffix;

    /** @var string */
    private $extension;

    /**
     * File constructor.
     * @param string $extension
     * @param string|null $prefix
     * @param string|null $suffix
     */
    public function __construct(string $extension, ?string $prefix, ?string $suffix)
    {
        $this->prefix = $prefix;
        $this->suffix = $suffix;
        $this->extension = $extension;
    }

    /**
     * @return string|null
     */
    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    /**
     * @param string|null $prefix
     */
    public function setPrefix(?string $prefix): void
    {
        $this->prefix = $prefix;
    }

    /**
     * @return string|null
     */
    public function getSuffix(): ?string
    {
        return $this->suffix;
    }

    /**
     * @param string|null $suffix
     */
    public function setSuffix(?string $suffix): void
    {
        $this->suffix = $suffix;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * @param string $extension
     */
    public function setExtension(string $extension): void
    {
        $this->extension = $extension;
    }
}