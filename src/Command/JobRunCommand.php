<?php

namespace App\Command;

use App\Api\Gobie\Client;
use App\Manager\JobManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class JobRunCommand extends Command
{
    protected static $defaultName = 'app:job:run';

    private $jobManager;
    private $client;

    public function __construct(
        JobManager $jobManager,
        Client $client
    ) {
        $this->jobManager = $jobManager;
        $this->client = $client;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Run a job')
            ->addArgument('id', InputArgument::REQUIRED, 'ID of the job');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id = (int) $input->getArgument('id');
        $data = $this->client->getJob($id);

        $job = $this->jobManager->populate($id, $data);
        $this->jobManager->execute($job);

        return Command::SUCCESS;
    }
}
