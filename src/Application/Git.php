<?php

namespace App\Application;

use Symfony\Component\Process\Process;
use App\Parser\GitVersionParser;
use Composer\Semver\Comparator;
use App\Lexer\GitVersionLexer;

final class Git implements ApplicationInterface
{
    const MINIMUM_VERSION = '2.11.0';

    /** @var string|null */
    private $version = null;

    /** @var bool */
    private $isInstalled = false;

    /** @var bool */
    private $isSupported = false;

    /**
     * @param Process|null $process
     */
    public function __construct(?Process $process = null)
    {
        $process = $process ?? new Process(['git', '--version']);
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
     * @return string
     */
    public function getName(): string
    {
        return 'Git';
    }

    /**
     * @return string|null
     */
    public function getVersion(): ?string
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
     * @param string       $repo
     * @param string       $branch
     * @param string|null  $path
     * @param Process|null $process
     *
     * @return Process|null
     */
    public function clone(
        string $repo, string $branch = 'master', ?string $path = null, ?Process $process = null
    ): ?Process {
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

            $process = $process ?? new Process($command);
            $process->run(function ($type, $buffer) {
                if (Process::ERR === $type) {
                    echo 'ERR > '.$buffer;
                } else {
                    echo 'OUT > '.$buffer;
                }
            });

            return $process;
        }

        return null;
    }
}
