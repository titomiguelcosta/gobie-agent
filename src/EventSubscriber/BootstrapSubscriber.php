<?php

namespace App\EventSubscriber;

use App\Api\Gobie\Client;
use App\Application\Composer;
use App\Application\Git;
use App\Event\JobBootEvent;
use App\Event\JobEvents;
use App\Model\Job;
use App\Util\DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class BootstrapSubscriber implements EventSubscriberInterface
{
    private $git;
    private $composer;
    private $client;
    private $dateTime;

    public function __construct(Git $git, Composer $composer, Client $client, DateTime $dateTime)
    {
        $this->git = $git;
        $this->composer = $composer;
        $this->client = $client;
        $this->dateTime = $dateTime;
    }

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

            $metadata['commit_hash'] = $this->git->getCommitHash($metadata['path']);
            $this->client->putJob($job->getId(), [
                'commitHash' => $metadata['commit_hash'],
            ]);
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
     * To manage the GitHub token.
     *
     * @see https://www.previousnext.com.au/blog/managing-composer-github-access-personal-access-tokens
     */
    public function composerInstall(JobBootEvent $event): void
    {
        $metadata = $event->getMetadata();

        if (file_exists(sprintf('%s/composer.json', $metadata['path']))) {
            printf('About to install composer dependencies%s', PHP_EOL);
            $this->composer->install($metadata['path']);
        } else {
            printf('Project is not using composer%s', PHP_EOL);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            JobEvents::BOOT_EVENT => [
                ['cloneRepo', 100],
                ['composerInstall', 90],
            ],
        ];
    }
}
