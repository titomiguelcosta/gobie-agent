<?php

namespace App\Application;

use Symfony\Component\Process\Process;
use App\Parser\DockerVersionParser;
use Composer\Semver\Comparator;

final class Docker implements ApplicationInterface
{
    const MINIMUM_VERSION = '18.09.3';

    /** @var null|string */
    private $version = null;

    /** @var null|string */
    private $build = null;

    /** @var bool */
    private $isInstalled = false;

    /** @var bool */
    private $isSupported = false;

    public function __construct()
    {
        $process = new Process('docker --version');
        $process->run();

        if ($process->isSuccessful()) {
            $this->isInstalled = true;
        }
        
        $dockerVersionParser = new DockerVersionParser($process->getOutput());
        $this->version = $dockerVersionParser->getVersion();
        $this->build = $dockerVersionParser->getBuild();

        if (Comparator::greaterThanOrEqualTo($dockerVersionParser->getVersion(), self::MINIMUM_VERSION)) {
            $this->isSupported = true;
        }
    }   

    /**
     * @return null|string
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * @return null|string
     */
    public function getBuild(): ?string
    {
        return $this->build;
    }
    
    /**
     * @return bool
     */
    public function isInstalled(): bool
    {
        return $this->isInstalled;
    }
    
    /**
     * @return bool
     */
    public function isSupported(): bool
    {
        return $this->isSupported;
    }
}