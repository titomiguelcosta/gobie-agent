<?php

namespace App\Application;

use App\Lexer\ComposerVersionLexer;
use App\Parser\ComposerVersionParser;
use Composer\Semver\Comparator;
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

    public function __construct(?Process $process = null)
    {
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
            if (Process::ERR === $type) {
                echo 'Composer ERR > '.$buffer;
            } else {
                echo 'Composer OUT > '.$buffer;
            }
        });

        return $process;
    }
}
