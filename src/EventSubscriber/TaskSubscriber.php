<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\ProjectExecuteEvent;
use App\Event\ProjectShutdownEvent;
use App\Event\ProjectEvents;
use App\Application\Docker;

class TaskSubscriber implements EventSubscriberInterface
{
    public function executeTasks(ProjectExecuteEvent $event): void
    {
        $metadata = $event->getMetadata();
        /** @var Docker */
        $docker = $metadata['docker'];
        
        $tasks = $event->getProject()->getTasks();
        foreach ($tasks as $task) {
            $process = $docker->exec($task->getCommand());
            printf("Executing tasks %s by running %s.%s", $task->getName(), $task->getCommand(), PHP_EOL);
            $task->setProcess($process);
        }
    }

    public function storeResults(ProjectShutdownEvent $event): void
    {
        printf("Store result of tasks.%s", PHP_EOL);
    }

    public static function getSubscribedEvents()
    {
        return [
            ProjectEvents::EXECUTE_EVENT => 'executeTasks',
            ProjectEvents::SHUTDOWN_EVENT => ['storeResults', 100],
        ];
    }
}
