<?php

namespace App\Event;

use Symfony\Component\EventDispatcher\Event;
use App\Model\Job;
use ArrayObject;

abstract class BaseJobEvent extends Event
{
    protected $job;
    protected $metadata;

    /**
     * @param Job         $job
     * @param ArrayObject $metadata
     */
    public function __construct(Job $job, ArrayObject $metadata)
    {
        $this->job = $job;
        $this->metadata = $metadata;
    }

    /**
     * @return Job
     */
    public function getJob(): Job
    {
        return $this->job;
    }

    /**
     * @return ArrayObject
     */
    public function getMetadata(): ArrayObject
    {
        return $this->metadata;
    }
}
