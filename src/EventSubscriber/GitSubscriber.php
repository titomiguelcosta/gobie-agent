<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\ProjectBootEvent;
use App\Event\ProjectEvents;

class GitSubscriber implements EventSubscriberInterface
{
    public function cloneRepo(ProjectBootEvent $event): void
    {
        $project = $event->getProject();
        $metadata = $event->getMetadata();
        printf("Clone project: %s.%s", $project->getRepo(), PHP_EOL);
        $metadata['path'] = sprintf('%s/%s', sys_get_temp_dir(), uniqid());
    }

    public static function getSubscribedEvents()
    {
        return [
           ProjectEvents::BOOT_EVENT => ['cloneRepo', 100],
        ];
    }
}
