<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Application\Docker;

class DependenciesCommand extends Command
{
    protected static $defaultName = 'app:dependencies';

    protected function configure()
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Checks projects meets all the dependencies')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->checkDocker($io);
    }

    /**
     * @param SymfonyStyle $io
     * @return Docker
     */
    protected function checkDocker(SymfonyStyle $io): Docker
    {
        $docker = new Docker();

        if (!$docker->isInstalled()) {
            $io->error("Docker is not installed");

            return $docker;
        } 
    
        if (!$docker->isSupported()) {
            $io->error(sprintf('Please update your version of Docker. Using %s, needed at least 18.09.3', $$docker->getVersion()));
        
            return $docker;
        }

        $io->success(
            sprintf(
                'You are running docker version %s and build %s',
                $docker->getVersion(),
                $docker->getBuild()
            )
        );

        return $docker;
    }
}
