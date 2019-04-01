<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\ProjectBootEvent;
use App\Event\ProjectEvents;
use App\Application\Git;

class GitSubscriber implements EventSubscriberInterface
{
    private $git;

    /**
     * @param Git $git
     */
    public function __construct(Git $git)
    {
        $this->git = $git;
    }

    public function cloneRepo(ProjectBootEvent $event): void
    {
        $project = $event->getProject();
        $metadata = $event->getMetadata();
        
        $cloned = $this->git->clone($project->getRepo(), 'master', $metadata['path']);

        if ($cloned) {
            printf("Cloned project: %s at %s.%s", $project->getRepo(), $metadata['path'], PHP_EOL);
        } else {
            print('Failed to cloned repo.');
            $event->stopPropagation();
        }
    }

    public static function getSubscribedEvents()
    {
        return [
           ProjectEvents::BOOT_EVENT => ['cloneRepo', 600],
        ];
    }
}
