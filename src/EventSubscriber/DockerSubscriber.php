<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\ProjectEvents;
use App\Event\ProjectBootEvent;
use App\Event\ProjectShutdownEvent;
use App\Application\Docker;

class DockerSubscriber implements EventSubscriberInterface
{
    /** @var Docker */
    private $docker;
    
    public function runDocker(ProjectBootEvent $event)
    {
        $metadata = $event->getMetadata();
        
        $this->docker = new Docker($metadata['id'], 'titomiguelcosta/grooming-chimps-php73', $metadata['path']);
        $running = $this->docker->run();

        if ($running) {
            $metadata['docker'] = $this->docker;
            printf("Running docker named %s.%s", $this->docker->getName(), PHP_EOL);
        } else {
            printf('It failed to start docker.%s', PHP_EOL);
            $event->stopPropagation();
        }
    }

    public function destroyDocker(ProjectShutdownEvent $event)
    {
        $this->docker->destroy();
        printf("Destroyed docker with name %s.%s", $this->docker->getName(), PHP_EOL);
    }

    public static function getSubscribedEvents()
    {
        return [
           ProjectEvents::BOOT_EVENT => [
                ['runDocker', 10],
           ],
           ProjectEvents::SHUTDOWN_EVENT => ['destroyDocker', 20],
        ];
    }
}

