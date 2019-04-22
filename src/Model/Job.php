<?php

namespace App\Model;

class Job
{
    protected $name;

    protected $description;

    protected $repo;

    protected $branch;

    protected $services;

    protected $tasks;

    public function __construct(string $repo, string $branch)
    {
        $this->name = 'demo';
        $this->repo = $repo;
        $this->branch = $branch;
        $this->tasks = [];
        $this->services = [];
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
