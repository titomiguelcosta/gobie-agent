<?php

namespace App\EventSubscriber;

use App\Api\Gobie\Client;
use App\Event\JobBootEvent;
use App\Event\JobEvents;
use App\Event\JobShutdownEvent;
use App\Model\Job;
use App\Util\DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JobSubscriber implements EventSubscriberInterface
{
    private $client;
    private $dateTime;

    public function __construct(Client $client, DateTime $dateTime)
    {
        $this->client = $client;
        $this->dateTime = $dateTime;
    }

    public function startedJob(JobBootEvent $event): void
    {
        $job = $event->getJob();

        $this->client->putJob($job->getId(), [
            'status' => Job::STATUS_STARTED,
            'startedAt' => $this->dateTime->now(),
        ]);
    }

    public function finishedJob(JobShutdownEvent $event): void
    {
        $job = $event->getJob();
        $this->client->putJob($job->getId(), [
            'status' => Job::STATUS_FINISHED,
            'finishedAt' => $this->dateTime->now(),
        ]);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            JobEvents::BOOT_EVENT => ['startedJob', 500],
            JobEvents::SHUTDOWN_EVENT => ['finishedJob', 1],
        ];
    }
}
