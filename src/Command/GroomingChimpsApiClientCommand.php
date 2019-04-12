<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Api\GroomingChimps\Client;

class GroomingChimpsApiClientCommand extends Command
{
    protected static $defaultName = 'grooming-chimps:api:client';

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
            ->setDescription('Playground for the Grooming Chimps API')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->text($this->client->getJobs());
    }
}
