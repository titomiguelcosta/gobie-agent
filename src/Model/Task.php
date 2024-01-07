<?php

namespace App\Model;

use Symfony\Component\Process\Process;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

final class Task
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_RUNNING = 'running';
    public const STATUS_ABORTED = 'aborted';
    public const STATUS_FAILED = 'failed';
    public const STATUS_SUCCEEDED = 'succeeded';

    private $id;
    private $name;
    private $command;
    private $options;
    private $process;

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

    public function getParsedCommand(array $metadata): string
    {
        $twig = new Environment(new ArrayLoader(['command' => $this->getCommand()]));
        $command = $twig->render('command', array_merge($metadata, $this->getOptions()));

        return $command;
    }
}
