<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\JobBootEvent;
use App\Event\JobEvents;
use App\Application\Git;
use App\Api\GroomingChimps\Client;
use App\Model\Job;
use App\Util\DateTime;
use Symfony\Component\Process\Process;

final class BootstrapSubscriber implements EventSubscriberInterface
{
    private $git;
    private $client;
    private $dateTime;

    /**
     * @param Git    $git
     * @param Client $client
     */
    public function __construct(Git $git, Client $client, DateTime $dateTime)
    {
        $this->git = $git;
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
    public function cloneRepo(JobBootEvent $event): void
    {
        $job = $event->getJob();
        $metadata = $event->getMetadata();

        $process = $this->git->clone($job->getRepo(), $job->getBranch(), $metadata['path']);

        if (null === $process) {
            echo 'Git is not installed.'.PHP_EOL;
            $this->client->putJob($job->getId(), [
                'status' => Job::STATUS_ABORTED,
                'errors' => ['Git is not installed'],
                'finishedAt' => $this->dateTime->now(),
            ]);
            $event->stopPropagation();
        } elseif ($process->isSuccessful()) {
            printf('Cloned job: %s at %s.%s', $job->getRepo(), $metadata['path'], PHP_EOL);
        } else {
            echo 'Failed to clone repo.'.PHP_EOL;
            $this->client->putJob($job->getId(), [
                'status' => Job::STATUS_ABORTED,
                'errors' => ['Failed to clone repo'],
                'finishedAt' => $this->dateTime->now(),
            ]);
            $event->stopPropagation();
        }
    }

    /**
     * ToDo: Create application for composer and inject in constructor, just like git
     * To manage the GitHub token.
     *
     * @see https://www.previousnext.com.au/blog/managing-composer-github-access-personal-access-tokens
     *
     * @param JobBootEvent $event
     * @param Process|null $process
     */
    public function composerInstall(JobBootEvent $event): void
    {
        $metadata = $event->getMetadata();

        if (file_exists(sprintf('%s/composer.json', $metadata['path']))) {
            printf('About to install composer dependencies%s', PHP_EOL);
            $process = new Process(['composer', 'install', '--no-interaction', '--no-progress', '--ignore-platform-reqs'], $metadata['path']);
            $process->setTimeout(null);
            $process->setIdleTimeout(null);
            $process->run(function ($type, $buffer) {
                if (Process::ERR === $type) {
                    echo 'ERR > '.$buffer;
                } else {
                    echo 'OUT > '.$buffer;
                }
            });
        } else {
            printf('Project is not using composer%s', PHP_EOL);
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            JobEvents::BOOT_EVENT => [
                ['cloneRepo', 100],
                ['composerInstall', 90],
            ],
        ];
    }
}
