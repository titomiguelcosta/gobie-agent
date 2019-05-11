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

    /**
     * @param Client   $client
     * @param DateTime $dateTime
     */
    public function __construct(Client $client, DateTime $dateTime)
    {
        $this->client = $client;
        $this->dateTime = $dateTime;
    }

    /**
     * @param JobBootEvent $event
     *
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function startedJob(JobBootEvent $event): void
    {
        $job = $event->getJob();
        $this->client->putJob($job->getId(), [
            'status' => Job::STATUS_STARTED,
            'startedAt' => $this->dateTime->now(),
        ]);
    }

    /**
     * @param JobShutdownEvent $event
     *
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
