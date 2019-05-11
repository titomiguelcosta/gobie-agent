<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\JobBootEvent;
use App\Event\JobEvents;

class ConfigureSubscriber implements EventSubscriberInterface
{
    public function configuration(JobBootEvent $event): void
    {
        $metadata = $event->getMetadata();
        $metadata['id'] = uniqid();
        $metadata['path'] = sprintf('%s/%s', sys_get_temp_dir(), $metadata['id']);

        printf('Setting path for the job at: %s.%s', $metadata['path'], PHP_EOL);
    }

    public static function getSubscribedEvents()
    {
        return [
            JobEvents::BOOT_EVENT => ['configuration', 999],
        ];
    }
}
