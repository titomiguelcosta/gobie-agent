<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Application\Git;

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
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->checkGit($io);
    }

    /**
     * @param SymfonyStyle $io
     *
     * @return Git
     */
    private function checkGit(SymfonyStyle $io): Git
    {
        $git = new Git();

        if (!$git->isInstalled()) {
            $io->error('Git is not installed');

            return $git;
        }

        if (!$git->isSupported()) {
            $io->error(sprintf('Please update your version of Git. Using %s, needed at least %s', $git->getVersion(), Git::MINIMUM_VERSION));

            return $git;
        }

        $io->success(
            sprintf('You are running git version %s', $git->getVersion())
        );

        return $git;
    }
}
