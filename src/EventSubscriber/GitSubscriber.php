<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\JobBootEvent;
use App\Event\JobEvents;
use App\Application\Git;
use App\Api\GroomingChimps\Client;
use App\Model\Job;
use App\Util\DateTime;

class GitSubscriber implements EventSubscriberInterface
{
    private $git;
    private $client;
    private $dateTime;

    /**
     * @param Git $git
     * @param Client $client
     */
    public function __construct(Git $git, Client $client, DateTime $dateTime)
    {
        $this->git = $git;
        $this->client = $client;
        $this->dateTime = $dateTime;
    }

    public function cloneRepo(JobBootEvent $event): void
    {
        $job = $event->getJob();
        $metadata = $event->getMetadata();

        $process = $this->git->clone($job->getRepo(), $job->getBranch(), $metadata['path']);

        if (null === $process) {
            print('Git is not installed.'.PHP_EOL);
            $this->client->putJob($job->getId(), [
                'status' => Job::STATUS_ABORTED,
                'errors' => ['Git is not installed'],
                'finishedAt' => $this->dateTime->now(),
            ]);
            $event->stopPropagation();
        } elseif ($process->isSuccessful()) {
            printf("Cloned job: %s at %s.%s", $job->getRepo(), $metadata['path'], PHP_EOL);
        } else {
            print('Failed to clone repo.'.PHP_EOL);
            $this->client->putJob($job->getId(), [
                'status' => Job::STATUS_ABORTED,
                'errors' => ['Failed to clone repo'],
                'finishedAt' => $this->dateTime->now(),
            ]);
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
