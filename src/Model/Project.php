<?php

namespace App\Model;

class Project
{
    protected $name;

    protected $description;

    protected $repo;

    protected $image;

    protected $services;

    protected $tasks;

    public function __construct(string $name, string $repo, string $image)
    {
        $this->name = $name;
        $this->repo = $repo;
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
