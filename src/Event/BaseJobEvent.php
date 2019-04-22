<?php

namespace App\Event;

use Symfony\Component\EventDispatcher\Event;
use App\Model\Job;
use ArrayObject;

abstract class BaseJobEvent extends Event
{
    protected $job;
    protected $metadata;

    public function __construct(Job $job, ArrayObject $metadata)
    {
        $this->job = $job;
        $this->metadata = $metadata;
    }

    public function getJob(): Job
    {
        return $this->job;
    }

    public function getMetadata(): ArrayObject
    {
        return $this->metadata;
    }
}
