<?php

namespace App\Model;

use Symfony\Component\Process\Process;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

final class Task
{
    const STATUS_PENDING = 'pending';
    const STATUS_RUNNING = 'running';
    const STATUS_ABORTED = 'aborted';
    const STATUS_FAILED = 'failed';
    const STATUS_SUCCEEDED = 'succeeded';

    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $command;

    /** @var array|null */
    private $options;

    /** @var null */
    private $process = null;

    public function __construct(int $id, string $name, string $command, ?array $options)
    {
        $this->id = $id;
        $this->name = $name;
        $this->command = $command;
        $this->options = $options ?? [];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setProcess(Process $process)
    {
        $this->process = $process;
    }

    public function getProcess(): ?Process
    {
        return $this->process;
    }

    public function shouldCwd(): bool
    {
        return array_key_exists('cwd', $this->options) && $this->options['cwd'];
    }

    /**
     * @param array $options
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function getParsedCommand(array $metadata): string
    {
        $twig = new Environment(new ArrayLoader(['command' => $this->getCommand()]));
        $command = $twig->render('command', array_merge($metadata, $this->getOptions()));

        return $command;
    }
}
