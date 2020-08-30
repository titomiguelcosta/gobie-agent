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
    private $tasks;

    public function __construct(int $id, string $repo, string $branch)
    {
        $this->id = $id;
        $this->name = 'demo';
        $this->repo = $repo;
        $this->branch = $branch;
        $this->tasks = [];
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

    /**
     * @return Task[]
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }
}
