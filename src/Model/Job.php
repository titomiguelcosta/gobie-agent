<?php

namespace App\Model;

final class Job
{
    const STATUS_PENDING = 'pending';
    const STATUS_STARTED = 'started';
    const STATUS_FINISHED = 'finished';
    const STATUS_ABORTED = 'aborted';

    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $repo;

    /** @var string */
    private $branch;

    /** @var array */
    private $services;

    /** @var array */
    private $tasks;

    /**
     * @param int    $id
     * @param string $repo
     * @param string $branch
     */
    public function __construct(int $id, string $repo, string $branch)
    {
        $this->id = $id;
        $this->name = 'demo';
        $this->repo = $repo;
        $this->branch = $branch;
        $this->tasks = [];
        $this->services = [];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getRepo(): string
    {
        return $this->repo;
    }

    /**
     * @return string
     */
    public function getBranch(): string
    {
        return $this->branch;
    }

    /**
     * @param Task $task
     */
    public function addTask(Task $task): void
    {
        $this->tasks[] = $task;
    }

    /**
     * @return Task[]
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }
}
