<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Model\Project;
use App\Model\Task;
use App\Manager\ProjectManager;
use Symfony\Component\Console\Input\InputOption;
use Composer\XdebugHandler\Process;

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
            ->addOption('branch', null, InputOption::VALUE_OPTIONAL, 'Name of the branch to clone', 'master')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $id = $input->getArgument('id');

        $manager = new ProjectManager($this->eventDispatcher);

        $project = new Project('Grooming Chimps', 'https://github.com/titomiguelcosta/lock.git', 'titomiguelcosta/grooming-chimps-php73');
        $project->addTask(new Task('system:ls', 'ls -al'));
        $project->addTask(new Task('system:pwd', 'pwd'));

        foreach ($project->getTasks() as $task) {
            if ($task->getProcess() instanceof Process) {
                printf('%s: %s.%s', $task->getName(), $task->getProcess()->getOutput(), PHP_EOL);
            }
        }

        $manager->execute($project);
    }
}
