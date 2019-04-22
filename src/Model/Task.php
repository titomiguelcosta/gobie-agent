<?php

namespace App\Model;

use Symfony\Component\Process\Process;

class Task
{
    protected $id;
    protected $name;
    protected $command;
    protected $process = null;

    public function __construct(int $id, string $name, string $command)
    {
        $this->id = $id;
        $this->name = $name;
        $this->command = $command;
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

    public function setProcess(Process $process)
    {
        $this->process = $process;
    }

    public function getProcess(): ?Process
    {
        return $this->process;
    }
}
