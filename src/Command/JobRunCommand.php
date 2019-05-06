<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Model\Job;
use App\Model\Task;
use App\Manager\JobManager;
use App\Api\GroomingChimps\Client;

class JobRunCommand extends Command
{
    protected static $defaultName = 'app:job:run';

    private $eventDispatcher;
    private $jobManager;
    private $client;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        JobManager $jobManager,
        Client $client
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->jobManager = $jobManager;
        $this->client = $client;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Run a job')
            ->addArgument('id', InputArgument::REQUIRED, 'ID of the job');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = (int) $input->getArgument('id');
        $jobModel = $this->client->getJob($id);

        $job = new Job($id, $jobModel['project']['repo'], $jobModel['branch']);
        foreach ($jobModel['tasks'] as $taskData) {
            $task = new Task($taskData['id'], $taskData['tool'], $taskData['command'], $taskData['options']);

            $job->addTask($task);
        }

        $this->jobManager->execute($job);
    }
}