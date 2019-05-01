<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\JobShutdownEvent;
use App\Event\JobEvents;
use App\Api\GroomingChimps\Client;
use App\Model\Job;
use App\Event\JobBootEvent;
use App\Util\DateTime;

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

    public static function getSubscribedEvents()
    {
        return [
            JobEvents::BOOT_EVENT => ['startedJob', 100],
            JobEvents::SHUTDOWN_EVENT => ['finishedJob', 1],
        ];
    }
}
