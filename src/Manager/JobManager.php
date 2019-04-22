<?php

namespace App\Manager;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Model\Job;
use App\Event\JobEvents;
use App\Event\JobBootEvent;
use App\Event\JobExecuteEvent;
use App\Event\JobShutdownEvent;
use ArrayObject;

final class JobManager
{
    private $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param Job $job
     */
    public function execute(Job $job): void
    {
        $metadata = new ArrayObject();
        $bootEvent = new JobBootEvent($job, $metadata);
        $this->eventDispatcher->dispatch(JobEvents::BOOT_EVENT, $bootEvent);

        if (!$bootEvent->isPropagationStopped()) {
            $executeEvent = new JobExecuteEvent($job, $metadata);

            $this->eventDispatcher->dispatch(JobEvents::EXECUTE_EVENT, $executeEvent);
            if (!$executeEvent->isPropagationStopped()) {
                $shutdownEvent = new JobShutdownEvent($job, $metadata);

                $this->eventDispatcher->dispatch(JobEvents::SHUTDOWN_EVENT, $shutdownEvent);
            }
        }
    }
}
