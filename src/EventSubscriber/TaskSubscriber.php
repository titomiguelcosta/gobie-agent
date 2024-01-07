<?php

namespace App\EventSubscriber;

use App\Api\Gobie\Client;
use App\Event\JobEvents;
use App\Event\JobExecuteEvent;
use App\Model\Task;
use App\Util\DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Process\Process;

class TaskSubscriber implements EventSubscriberInterface
{
    public function __construct(private Client $client, private DateTime $dateTime)
    {
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

            $process = Process::fromShellCommandline(
                $task->getParsedCommand($metadata->getArrayCopy()),
                $task->shouldCwd() ? $metadata['path'] : null
            );
            $process->setTimeout(0);
            $process->setIdleTimeout(0);
            $task->setProcess($process);

            try {
                printf('Executing task %s by running %s.%s', $task->getName(), $task->getCommand(), PHP_EOL);
                $process->run();

                $this->client->putTask($task->getId(), [
                    'status' => $process->isSuccessful() ? Task::STATUS_SUCCEEDED : Task::STATUS_FAILED,
                    'output' => $this->parseOutput($process->getOutput(), $metadata['path']),
                    'errorOutput' => $this->parseOutput($process->getErrorOutput(), $metadata['path']),
                    'exitCode' => $process->getExitCode(),
                    'finishedAt' => $this->dateTime->now(),
                ]);
                printf('Stored result for task %s after running %s.%s', $task->getName(), $task->getCommand(), PHP_EOL);
            } catch (\Exception $exception) {
                printf('Exception thrown when running task %s with message %s.%s', $task->getName(), $exception->getMessage(), PHP_EOL);
                $this->client->putTask($task->getId(), [
                    'status' => Task::STATUS_FAILED,
                    'output' => '',
                    'errorOutput' => $exception->getMessage(),
                    'exitCode' => 1,
                    'finishedAt' => $this->dateTime->now(),
                ]);
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            JobEvents::EXECUTE_EVENT => 'executeTasks',
        ];
    }

    private function parseOutput(string $output, string $path): string
    {
        $output = trim($output);
        // remove basename from file paths
        $output = str_replace($path.'/', '', $output);

        $json = json_decode(
            $output,
            true,
            JSON_INVALID_UTF8_IGNORE
        );

        if (JSON_ERROR_NONE === json_last_error()) {
            $output = json_encode(
                $json,
                JSON_INVALID_UTF8_IGNORE | JSON_UNESCAPED_SLASHES
            );
        }

        return $output;
    }
}
