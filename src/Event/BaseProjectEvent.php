<?php

namespace App\Event;

use Symfony\Component\EventDispatcher\Event;
use App\Model\Project;
use ArrayObject;

abstract class BaseProjectEvent extends Event
{
    protected $project;
    protected $metadata;

    public function __construct(Project $project, ArrayObject $metadata)
    {
        $this->project = $project;
        $this->metadata = $metadata;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function getMetadata(): ArrayObject
    {
        return $this->metadata;
    }
}