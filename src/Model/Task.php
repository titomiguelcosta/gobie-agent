<?php

namespace App\Model;

use Symfony\Component\Process\Process;

class Task
{
    protected $name;
    protected $command;
    protected $process = null;

    public function __construct(string $name, string $command)
    {
        $this->name = $name;
        $this->command = $command;
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