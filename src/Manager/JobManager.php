<?php

namespace App\Manager;

use App\Event\JobBootEvent;
use App\Event\JobEvents;
use App\Event\JobExecuteEvent;
use App\Event\JobShutdownEvent;
use App\Model\Job;
use App\Model\Task;
use ArrayObject;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class JobManager
{
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function populate(int $id, array $data): Job
    {
        $job = new Job($id, $data['project']['repo'], $data['branch']);
        foreach ($data['tasks'] as $taskData) {
            $task = new Task($taskData['id'], $taskData['tool'], $taskData['command'], $taskData['options']);

            $job->addTask($task);
        }

        return $job;
    }

    public function execute(Job $job): void
    {
        $metadata = new ArrayObject();
        $bootEvent = new JobBootEvent($job, $metadata);
        $this->eventDispatcher->dispatch($bootEvent, JobEvents::BOOT_EVENT);

        if (!$bootEvent->isPropagationStopped()) {
            $executeEvent = new JobExecuteEvent($job, $metadata);

            $this->eventDispatcher->dispatch($executeEvent, JobEvents::EXECUTE_EVENT);
            if (!$executeEvent->isPropagationStopped()) {
                $shutdownEvent = new JobShutdownEvent($job, $metadata);

                $this->eventDispatcher->dispatch($shutdownEvent, JobEvents::SHUTDOWN_EVENT);
            }
        }
    }
}
