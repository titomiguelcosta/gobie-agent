<?php

namespace App\Model;

use Symfony\Component\Process\Process;

final class Task
{
    const STATUS_PENDING = 'pending';
    const STATUS_RUNNING = 'running';
    const STATUS_ABORTED = 'aborted';
    const STATUS_FAILED = 'failed';
    const STATUS_SUCCEEDED = 'succeeded';

    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $command;

    /** @var array|null */
    private $options;

    /** @var null */
    private $process = null;

    /**
     * @param int        $id
     * @param string     $name
     * @param string     $command
     * @param array|null $options
     */
    public function __construct(int $id, string $name, string $command, ?array $options)
    {
        $this->id = $id;
        $this->name = $name;
        $this->command = $command;
        $this->options = $options ?? [];
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
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param Process $process
     */
    public function setProcess(Process $process)
    {
        $this->process = $process;
    }

    /**
     * @return Process|null
     */
    public function getProcess(): ?Process
    {
        return $this->process;
    }

    /**
     * @return bool
     */
    public function shouldCwd(): bool
    {
        return array_key_exists('cwd', $this->options) && $this->options['cwd'];
    }
}
