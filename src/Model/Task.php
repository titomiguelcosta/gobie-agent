<?php

namespace App\Model;

class Task
{
    protected $name;

    protected $command;

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
}