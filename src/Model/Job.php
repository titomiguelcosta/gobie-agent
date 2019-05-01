<?php

namespace App\Model;

class Job
{
    const STATUS_PENDING = 'pending';
    const STATUS_STARTED = 'started';
    const STATUS_FINISHED = 'finished';
    const STATUS_ABORTED = 'aborted';

    private $id;
    private $name;
    private $repo;
    private $branch;
    private $services;
    private $tasks;

    public function __construct(int $id, string $repo, string $branch)
    {
        $this->id = $id;
        $this->name = 'demo';
        $this->repo = $repo;
        $this->branch = $branch;
        $this->tasks = [];
        $this->services = [];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRepo(): string
    {
        return $this->repo;
    }

    public function getBranch(): string
    {
        return $this->branch;
    }

    public function addTask(Task $task): void
    {
        $this->tasks[] = $task;
    }

    public function getTasks(): array
    {
        return $this->tasks;
    }

    public function addService(Service $service): void
    {
        $this->services[] = $service;
    }

    public function getServices(): array
    {
        return $this->services;
    }
}
