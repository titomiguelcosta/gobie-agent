<?php

namespace App\Application;

use App\Lexer\ComposerVersionLexer;
use App\Parser\ComposerVersionParser;
use Composer\Semver\Comparator;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;

final class Composer implements ApplicationInterface
{
    const MINIMUM_VERSION = '1.8.0';

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

        $process = $process ?? new Process(['composer', '--version']);
        $process->run();

        if ($process->isSuccessful()) {
            $this->isInstalled = true;
        }

        $composerVersionParser = new ComposerVersionParser(new ComposerVersionLexer($process->getOutput()));
        $this->version = $composerVersionParser->getVersion();

        if (Comparator::greaterThanOrEqualTo($composerVersionParser->getVersion(), self::MINIMUM_VERSION)) {
            $this->isSupported = true;
        }
    }

    public function getName(): string
    {
        return 'Composer';
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

    public function install(string $path, ?Process $process = null): Process
    {
        $process = $process ?? new Process(['composer', 'install', '--no-interaction', '--no-progress', '--ignore-platform-reqs'], $path);
        $process->setTimeout(0);
        $process->setIdleTimeout(0);
        $process->run(function ($type, $buffer) {
            if ($this->logger) {
                if (Process::ERR === $type) {
                    $this->logger->error('Composer ERR > ' . $buffer);
                } else {
                    $this->logger->debug('Composer OUT > ' . $buffer);
                }
            }
        });

        return $process;
    }
}
