<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Application\Docker;

$docker = new Docker('tito');
$docker->run();

echo $docker->isRunning() ? 'Running' : 'Stopped';
echo $docker->getProcess()->getPid();
echo $docker->getProcess()->getOutput();

echo "Sleeping before executing command";
$process = $docker->exec('ls -al');
echo $process->getOutput();

echo $docker->destroy() ? 'Stopped' : 'Running';
