<?php

namespace App\Command;

use App\Application\ApplicationInterface;
use App\Application\Composer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Application\Git;

class DependenciesCommand extends Command
{
    protected static $defaultName = 'app:dependencies';
    private $git;
    private $composer;

    /** @var SymfonyStyle */
    private $io;

    /**
     * @param Git $git
     * @param Composer $composer
     */
    public function __construct(Git $git, Composer $composer)
    {
        parent::__construct();
        $this->git = $git;
        $this->composer = $composer;
    }


    protected function configure()
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Checks environment meets all the dependencies')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->checkApplication($this->git);
        $this->checkApplication($this->composer);
    }

    /**
     * @param ApplicationInterface $app
     * @return ApplicationInterface
     */
    private function checkApplication(ApplicationInterface $app): ApplicationInterface
    {
        if (!$app->isInstalled()) {
            $this->io->error(sprintf('%s is not installed', $app->getName()));

            return $app;
        }

        if (!$app->isSupported()) {
            $this->io->error(
                sprintf('Please update your version of %s. Using %s.', $app->getName(), $app->getVersion())
            );

            return $app;
        }

        $this->io->success(
            sprintf('You are running %s version %s', $app->getName(), $app->getVersion())
        );

        return $app;
    }
}
