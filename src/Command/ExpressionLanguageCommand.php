<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class ExpressionLanguageCommand extends Command
{
    protected static $defaultName = 'app:expression-language';

    protected $expressionLanguage;

    public function __construct(Environment $twig)
    {
        $this->expressionLanguage = new ExpressionLanguage();
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Playground for the Symfony Expression Language Component')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $metadata = [
            'path' => '/tmp',
        ];

        $command = '"php bin/console lint:twig " ~ path ~ "/src"';
        $io->writeln($this->expressionLanguage->evaluate($command, $metadata));

        $command = 'php bin/console lint:twig {{ path }}/src';
        $twig = new Environment(new ArrayLoader(['command' => $command]));
        $io->writeln($twig->render('command', $metadata));
    }
}
