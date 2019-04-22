<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\JobExecuteEvent;
use App\Event\JobShutdownEvent;
use App\Event\JobEvents;
use Symfony\Component\Process\Process;
use App\Api\GroomingChimps\Client;

class TaskSubscriber implements EventSubscriberInterface
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function executeTasks(JobExecuteEvent $event): void
    {
        $tasks = $event->getJob()->getTasks();
        foreach ($tasks as $task) {
            $process = new Process($task->getCommand());
            $process->setTimeout(0);
            $process->setIdleTimeout(0);
            printf("Executing task %s by running %s.%s", $task->getName(), $task->getCommand(), PHP_EOL);
            $process->run();
            $task->setProcess($process);
        }
    }

    public function storeResults(JobShutdownEvent $event): void
    {
        $job = $event->getJob();
        foreach ($job->getTasks() as $task) {
            $process = $task->getProcess();
            if ($process instanceof Process) {
                $this->client->putTask($task->getId(), [
                    'output' => trim(preg_replace('/\s+/', ' ', $process->getOutput())),
                    'errorOutput' => trim(preg_replace('/\s+/', ' ', $process->getErrorOutput())),
                    'exitCode' => $process->getExitCode(),
                ]);
                printf("Stored result for task %s after running %s.%s", $task->getName(), $task->getCommand(), PHP_EOL);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            JobEvents::EXECUTE_EVENT => 'executeTasks',
            JobEvents::SHUTDOWN_EVENT => ['storeResults', 100],
        ];
    }
}
