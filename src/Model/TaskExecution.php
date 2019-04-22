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

    public function getFinishedAt(): ?\DateTimeInterface
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(\DateTimeImmutable $finishedAt): void
    {
        $this->finishedAt = $finishedAt;
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeImmutable $startedAt): void
    {
        $this->startedAt = $startedAt;
    }
}
