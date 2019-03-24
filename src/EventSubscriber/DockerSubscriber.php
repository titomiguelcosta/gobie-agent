<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\ProjectEvents;
use App\Event\ProjectBootEvent;
use App\Event\ProjectShutdownEvent;

class DockerSubscriber implements EventSubscriberInterface
{
    public function generateDockerCompose(ProjectBootEvent $event)
    {
        printf("Generate docker-compose from services in %s.%s", $event->getMetadata()['path'], PHP_EOL);
    }
    
    public function runDocker(ProjectBootEvent $event)
    {
        printf("Run docker.%s", PHP_EOL);
    }

    public function destroyDocker(ProjectShutdownEvent $event)
    {
        printf("Destroy docker.%s", PHP_EOL);
    }

    public static function getSubscribedEvents()
    {
        return [
           ProjectEvents::BOOT_EVENT => [
                ['generateDockerCompose', 40],
                ['runDocker', 10],
           ],
           ProjectEvents::SHUTDOWN_EVENT => ['destroyDocker', 20],
        ];
    }
}

