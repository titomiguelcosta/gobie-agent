<?php

namespace App\EventSubscriber;

use App\Api\GroomingChimps\Client;
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

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function startedJob(JobBootEvent $event): void
    {
        $job = $event->getJob();
        $metadata = $event->getMetadata();

        $this->client->putJob($job->getId(), [
            'status' => Job::STATUS_STARTED,
            'headSha' => $metadata['commit_hash'] ?? null,
            'startedAt' => $this->dateTime->now(),
        ]);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function finishedJob(JobShutdownEvent $event): void
    {
        $job = $event->getJob();
        $this->client->putJob($job->getId(), [
            'status' => Job::STATUS_FINISHED,
            'finishedAt' => $this->dateTime->now(),
        ]);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            JobEvents::BOOT_EVENT => ['startedJob', 500],
            JobEvents::SHUTDOWN_EVENT => ['finishedJob', 1],
        ];
    }
}
