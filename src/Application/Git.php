<?php

namespace App\Application;

use App\Lexer\GitVersionLexer;
use App\Parser\GitVersionParser;
use Composer\Semver\Comparator;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;

final class Git implements ApplicationInterface
{
    const MINIMUM_VERSION = '2.11.0';

    /** @var string|null */
    private $version = null;

    /** @var bool */
    private $isInstalled = false;

    /** @var bool */
    private $isSupported = false;

    /** @var LoggerInterface|null */
    private $logger;

    public function __construct(?Process $process = null, LoggerInterface $logger = null)
    {
        $this->logger = $logger;

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

    public function getName(): string
    {
        return 'Git';
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function isInstalled(): bool
    {
        return $this->isInstalled;
    }

    public function isSupported(): bool
    {
        return $this->isSupported;
    }

    public function clone(
        string $repo,
        string $branch = 'master',
        ?string $path = null,
        ?Process $process = null
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
                if ($this->logger) {
                    if (Process::ERR === $type) {
                        $this->logger->error('Git ERR > '.$buffer);
                    } else {
                        $this->logger->debug('Git OUT > '.$buffer);
                    }
                }
            });

            return $process;
        }

        return null;
    }

    public function getCommitHash(
        string $path,
        ?Process $process = null
    ): ?string {
        if ($this->isInstalled()) {
            $command = [
                'git',
                'rev-parse',
                'HEAD',
            ];

            $process = $process ?? new Process($command, $path);
            $process->run(function ($type, $buffer) {
                if ($this->logger) {
                    if (Process::ERR === $type) {
                        $this->logger->error('Git ERR > '.$buffer);
                    } else {
                        $this->logger->debug('Git OUT > '.$buffer);
                    }
                }
            });

            if ($process->isSuccessful()) {
                $commitHash = trim(preg_replace('/\s+/', ' ', $process->getOutput()));

                if ($this->logger) {
                    $this->logger->debug('Git commit hash: '.$commitHash);
                }

                return $commitHash;
            }
        }

        return null;
    }
}
