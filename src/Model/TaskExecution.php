<?php

namespace App\Model;

class TaskExecution
{
    protected $task;

    protected $output;

    protected $startedAt;

    protected $finishedAt;

    protected $exitCode;

    public function getTask(): Task
    {
        return $this->task;
    }

    public function setOutput(string $output): void
    {
        $this->output = $output;
    }

    public function setExitCode(int $exitCode): void
    {
        $this->exitCode = $exitCode;
    }

    public function getExitCode(): ?int
    {
        return $this->exitCode;
    }

    public function getExecutedAt(): ?\DateTimeInterface
    {
        return $this->executeAt;
    }

    public function setExecutedAt(\DateTimeImmutable $executedAt): void
    {
        $this->executeAt = $executeAt;
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->executeAt;
    }

    public function setStartedAt(\DateTimeImmutable $executedAt): void
    {
        $this->executeAt = $executeAt;
    }
}