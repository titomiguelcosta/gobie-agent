<?php

namespace App\Manager;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Model\Project;
use App\Event\ProjectEvents;
use App\Event\ProjectBootEvent;
use App\Event\ProjectExecuteEvent;
use App\Event\ProjectShutdownEvent;
use ArrayObject;

final class ProjectManager
{
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function execute(Project $project): void
    {
        $metadata = new ArrayObject();
        $bootEvent = new ProjectBootEvent($project, $metadata);
        $this->eventDispatcher->dispatch(ProjectEvents::BOOT_EVENT, $bootEvent);

        if (!$bootEvent->isPropagationStopped()) {
            $executeEvent = new ProjectExecuteEvent($project, $metadata);

            $this->eventDispatcher->dispatch(ProjectEvents::EXECUTE_EVENT, $executeEvent);
            if (!$executeEvent->isPropagationStopped()) {
                $shutdownEvent = new ProjectShutdownEvent($project, $metadata);

                $this->eventDispatcher->dispatch(ProjectEvents::SHUTDOWN_EVENT, $shutdownEvent);
            }
        }
    }
}