<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\JobBootEvent;
use App\Event\JobEvents;
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

    public function cloneRepo(JobBootEvent $event): void
    {
        $job = $event->getJob();
        $metadata = $event->getMetadata();

        $cloned = $this->git->clone($job->getRepo(), 'master', $metadata['path']);

        if ($cloned) {
            printf("Cloned job: %s at %s.%s", $job->getRepo(), $metadata['path'], PHP_EOL);
        } else {
            print('Failed to cloned repo.');
            $event->stopPropagation();
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            JobEvents::BOOT_EVENT => ['cloneRepo', 600],
        ];
    }
}
