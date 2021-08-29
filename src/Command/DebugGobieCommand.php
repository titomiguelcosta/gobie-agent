<?php

namespace App\Command;

use App\Api\Gobie\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DebugGobieCommand extends Command
{
    protected static $defaultName = 'app:debug';

    /** @var Client */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Playground for the Gobies API');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->text($this->client->getJob(1));

        return 0;
    }
}
