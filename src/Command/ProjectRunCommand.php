<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Model\Project;
use App\Model\Service;
use App\Model\Task;
use App\Manager\ProjectManager;

class ProjectRunCommand extends Command
{
    protected static $defaultName = 'app:project:run';

    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
        parent::__construct();
    }


    protected function configure()
    {
        $this
            ->setDescription('Run a project')
            ->addArgument('id', InputArgument::REQUIRED, 'ID of the project')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $id = $input->getArgument('id');

        $manager = new ProjectManager($this->eventDispatcher);

        $project = new Project('Grooming Chimps', 'https://github.com/titomiguelcosta/grooming-chimps', 'titomiguelcosta/grooming-chimps-php73');
        $project->addTask(new Task('composer:install', 'composer install'));
        $project->addTask(new Task('phpunit:run', 'php vendor/bin/phpunit'));

        $manager->execute($project);
    }
}
