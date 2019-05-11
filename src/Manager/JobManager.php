<?php

namespace App\Manager;

use App\Model\Task;
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
     * @param int   $id
     * @param array $data
     *
     * @return Job
     */
    public function populate(int $id, array $data): Job
    {
        $job = new Job($id, $data['project']['repo'], $data['branch']);
        foreach ($data['tasks'] as $taskData) {
            $task = new Task($taskData['id'], $taskData['tool'], $taskData['command'], $taskData['options']);

            $job->addTask($task);
        }

        return $job;
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
