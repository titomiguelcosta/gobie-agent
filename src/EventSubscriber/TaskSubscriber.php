<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\JobExecuteEvent;
use App\Event\JobEvents;
use Symfony\Component\Process\Process;
use App\Api\GroomingChimps\Client;
use App\Model\Task;
use App\Util\DateTime;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class TaskSubscriber implements EventSubscriberInterface
{
    private $client;
    private $dateTime;

    public function __construct(Client $client, DateTime $dateTime)
    {
        $this->client = $client;
        $this->dateTime = $dateTime;
    }

    public function executeTasks(JobExecuteEvent $event): void
    {
        $metadata = $event->getMetadata();
        $tasks = $event->getJob()->getTasks();
        foreach ($tasks as $task) {
            // notify API that we started running task
            $this->client->putTask($task->getId(), [
                'status' => Task::STATUS_RUNNING,
                'startedAt' => $this->dateTime->now(),
            ]);

            $twig = new Environment(new ArrayLoader(['command' => $task->getCommand()]));
            $process = Process::fromShellCommandline(
                $twig->render('command', array_merge($metadata->getArrayCopy(), $task->getOptions())),
                $task->shouldCwd() ? $metadata['path'] : null
            );
            $task->setProcess($process);
            $process->setTimeout(0);
            $process->setIdleTimeout(0);
            printf('Executing task %s by running %s.%s', $task->getName(), $task->getCommand(), PHP_EOL);

            try {
                $process->run();

                $this->client->putTask($task->getId(), [
                    'output' => trim(preg_replace('/\s+/', ' ', $process->getOutput())),
                    'errorOutput' => trim(preg_replace('/\s+/', ' ', $process->getErrorOutput())),
                    'exitCode' => $process->getExitCode(),
                    'status' => $process->isSuccessful() ? Task::STATUS_SUCCEEDED : Task::STATUS_FAILED,
                    'finishedAt' => $this->dateTime->now(),
                ]);
                printf('Stored result for task %s after running %s.%s', $task->getName(), $task->getCommand(), PHP_EOL);
            } catch (\Exception $exception) {
                printf('Exception thrown when running task %s with message %s.%s', $task->getName(), $exception->getMessage(), PHP_EOL);
                $this->client->putTask($task->getId(), [
                    'status' => Task::STATUS_FAILED,
                    'errorOutput' => $exception->getMessage(),
                    'exitCode' => 1,
                    'finishedAt' => $this->dateTime->now(),
                ]);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            JobEvents::EXECUTE_EVENT => 'executeTasks',
        ];
    }
}
