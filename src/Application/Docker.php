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

    /** @var string */
    private $path;

    /** @var string */
    private $containerId = null;

    /**
     * @param string $name Name of the container
     * @param string $image Docker image
     */
    public function __construct(string $name, string $image = 'titomiguelcosta/grooming-chimps-php73', string $path = null)
    {
        $this->name = $name;
        $this->image = $image;
        $this->path = null === $path ? \sys_get_temp_dir() : $path;

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
            $this->doRun($process);

            if ($process->isSuccessful()) {
                // run container
                $command = [
                    'docker',
                    'run',
                    '-p', '7000:7000',
                    '-v', $this->path . ':/project',
                    '--name', $this->name,
                    '-t', $this->image,
                ];
                $this->process = new Process(array_merge($command, $options));
                $this->doRun($this->process, true);

                // determine container id
                $command = sprintf('docker ps -aqf "name=%s"', $this->name);
                $process = new Process($command);
                do {
                    $this->doRun($process);
                    $this->containerId = $process->getOutput();
                } while (!$this->containerId && $this->isRunning());
            }
        }

        return $this->isRunning();
    }

    /**
     * @return bool
     */
    public function stop(): bool
    {
        if ($this->isRunning()) {
            $command = sprintf('docker stop --time 30 %s', $this->containerId);
            $process = new Process($command);
            $this->doRun($process);

            $this->process->stop();
        }

        return !$this->isRunning();
    }

    /**
     * @param array $options
     * @return bool
     */
    public function destroy(bool $image = false): bool
    {
        if ($this->isRunning()) {
            $this->stop();

            // destroy container
            $command = sprintf('docker rm %s', $this->containerId);
            $process = new Process($command);
            $this->doRun($process);

            if ($image) {
                // check process is running before forcing exit
                if ($this->isRunning()) {
                    $this->process->signal(SIGKILL);
                }

                // if not running, attempt to delete image
                if (!$this->isRunning()) {
                    $command = sprintf('docker rmi %s --force', $this->containerId);
                    $process = new Process($command);
                    $this->doRun($process);
                }
            }

            $this->process = null;
        }

        return !$this->isRunning();
    }

    /**
     * @param string $command
     * @return Process|null
     */
    public function exec(string $command): ? Process
    {
        if ($this->isRunning()) {
            $command = sprintf('docker exec -i %s %s', $this->name, $command);
            $process = new Process($command);
            $this->doRun($process);

            return $process;
        }

        return null;
    }

    /**
     * @param Process $process
     */
    private function doRun(Process $process, bool $start = false): void
    {
        $output = function ($type, $buffer) {
            if (Process::ERR === $type) {
                echo 'ERR > ' . $buffer;
            } else {
                echo 'OUT > ' . $buffer;
            }
        };
        $process->setTimeout(null);
        $process->setIdleTimeout(null);

        $start ? $process->start($output) : $process->run($output);
    }
}
