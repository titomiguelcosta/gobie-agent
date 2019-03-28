<?php

namespace App\Application;

use Symfony\Component\Process\Process;
use App\Parser\DockerVersionParser;
use Composer\Semver\Comparator;

/**
 * By design, one object per docker container
 */
final class Docker implements ApplicationInterface
{
    const MINIMUM_VERSION = '18.03.0';

    /** @var null|string */
    private $version = null;

    /** @var null|string */
    private $build = null;

    /** @var bool */
    private $isInstalled = false;

    /** @var bool */
    private $isSupported = false;

    /** @var Process */
    private $process = null;

    /** @var string */
    private $name;

    /** @var string */
    private $image;

    /**
     * @param string $name Name of the container
     * @param string $image Docker image
     */
    public function __construct(string $name, string $image = 'titomiguelcosta/grooming-chimps-php73')
    {
        $this->name = $name;
        $this->image = $image;

        $process = new Process(['docker', '--version']);
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
    public function getVersion(): ? string
    {
        return $this->version;
    }

    /**
     * @return null|string
     */
    public function getBuild(): ? string
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

    /**
     * @return bool
     */
    public function isRunning(): bool
    {
        return $this->process instanceof Process && $this->process->isRunning();
    }

    /**
     * @return Process|null
     */
    public function getProcess(): ? Process
    {
        return $this->process;
    }

    /**
     * @param array $options
     * @return bool
     */
    public function run(array $options = []): bool
    {
        if (!$this->isRunning()) {
            // pull image before attempting to run
            $process = new Process([
                'docker',
                'pull',
                $this->image
            ]);
            $process->setTimeout(null);
            $process->run(function ($type, $buffer) {
                if (Process::ERR === $type) {
                    echo 'ERR > ' . $buffer;
                } else {
                    echo 'OUT > ' . $buffer;
                }
            });

            if ($process->isSuccessful()) {
                // run container
                $command = ['docker', 'run', '--name', $this->name, '-t', $this->image, ];
                $this->process = new Process(array_merge($command, $options));
                $this->process->setTimeout(null);
                $this->process->setIdleTimeout(null);
                $this->process->start();
            }
        }

        return $this->isRunning();
    }

    /**
     * @param array $options
     * @return bool
     */
    public function stop(array $options = []): bool
    {
        if ($this->isRunning()) {
            $command = array_merge([
                'docker',
                'stop',
                '--name',
                $this->name
            ], $options);
            $process = new Process($command);
            $process->run();
            $this->process->stop();
        }

        return !$this->isRunning();
    }

    /**
     * @param array $options
     * @return bool
     */
    public function destroy(array $options = []): bool
    {
        if ($this->isRunning()) {
            $command = array_merge([
                'docker',
                'rmi',
                '--name',
                $this->name
            ], $options);
            $process = new Process($command);
            $process->run();

            $this->process->signal(SIGKILL);
            $this->process = null;
        }

        return !$this->process->isRunning();
    }
}
