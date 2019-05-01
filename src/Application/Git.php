<?php

namespace App\Application;

use Symfony\Component\Process\Process;
use App\Parser\GitVersionParser;
use Composer\Semver\Comparator;
use App\Lexer\GitVersionLexer;

class Git implements ApplicationInterface
{
    const MINIMUM_VERSION = '2.11.0';

    /** @var null|string */
    private $version = null;

    /** @var bool */
    private $isInstalled = false;

    /** @var bool */
    private $isSupported = false;

    public function __construct()
    {
        $process = new Process(['git', '--version']);
        $process->run();

        if ($process->isSuccessful()) {
            $this->isInstalled = true;
        }

        $gitVersionParser = new GitVersionParser(new GitVersionLexer($process->getOutput()));
        $this->version = $gitVersionParser->getVersion();

        if (Comparator::greaterThanOrEqualTo($gitVersionParser->getVersion(), self::MINIMUM_VERSION)) {
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
     * @param string $repo URL of repo
     * @param string $branch Name of the branch
     * @param string $path Where to clone repo into
     * @return null|Process
     */
    public function clone(string $repo, string $branch = 'master', string $path = null): ?Process
    {
        if ($this->isInstalled()) {
            $path = null === $path ? sys_get_temp_dir() : $path;

            $command = [
                'git',
                'clone',
                '--single-branch',
                '--depth',
                '1',
                '--branch',
                $branch,
                $repo,
                $path,
            ];

            $process = new Process($command);
            $process->run(function ($type, $buffer) {
                if (Process::ERR === $type) {
                    echo 'ERR > ' . $buffer;
                } else {
                    echo 'OUT > ' . $buffer;
                }
            });

            return $process;
        }

        return null;
    }
}
