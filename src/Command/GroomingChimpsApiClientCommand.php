<?php

namespace App\Command;

use App\Api\GroomingChimps\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GroomingChimpsApiClientCommand extends Command
{
    protected static $defaultName = 'app:grooming-chimps:api:client';

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
            ->setDescription('Playground for the Grooming Chimps API');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->text($this->client->getJob(1));

        return 0;
    }
}
