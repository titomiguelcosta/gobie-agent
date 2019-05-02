<?php

namespace App\Model;

use Symfony\Component\Process\Process;

class Task
{
    const STATUS_PENDING = 'pending';
    const STATUS_RUNNING = 'running';
    const STATUS_ABORTED = 'aborted';
    const STATUS_FAILED = 'failed';
    const STATUS_SUCCEEDED = 'succeeded';

    protected $id;
    protected $name;
    protected $command;
    protected $options;
    protected $process = null;

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
}
