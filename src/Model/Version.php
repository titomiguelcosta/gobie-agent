<?php

namespace App\Model;

class Version
{
    private $major;
    private $minor;
    private $patch;

    /**
     * @param string $major
     * @param string $minor
     * @param string $patch
     */
    public function __construct(string $major, string $minor, string $patch)
    {
        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
    }

    /**
     * @return string
     */
    public function getMajor(): string
    {
        return $this->major;
    }

    /**
     * @return string
     */
    public function getMinor(): string
    {
        return $this->minor;
    }

    /**
     * @return string
     */
    public function getPatch(): string
    {
        return $this->patch;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return sprintf('%s.%s.%s', $this->major, $this->minor, $this->patch);
    }
}
