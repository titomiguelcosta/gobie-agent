<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\ProjectBootEvent;
use App\Event\ProjectEvents;

class ConfigureSubscriber implements EventSubscriberInterface
{
    public function configuration(ProjectBootEvent $event): void
    {
        $metadata = $event->getMetadata();
        $metadata['id'] = uniqid();
        $metadata['path'] = sprintf('%s/%s', sys_get_temp_dir(), $metadata['id']);

        printf("Setting path for the project at: %s.%s", $metadata['path'], PHP_EOL);
    }

    public static function getSubscribedEvents()
    {
        return [
           ProjectEvents::BOOT_EVENT => ['configuration', 999],
        ];
    }
}
