<?php

namespace App\Event;

use App\Model\Job;
use ArrayObject;
use Symfony\Component\EventDispatcher\GenericEvent;

abstract class BaseJobEvent extends GenericEvent
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
