<?php

namespace App\Application;

use Symfony\Component\Process\Process;
use App\Parser\ComposerVersionParser;
use Composer\Semver\Comparator;
use App\Lexer\ComposerVersionLexer;

final class Composer implements ApplicationInterface
{
    const MINIMUM_VERSION = '1.6.0';

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

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'Composer';
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
}