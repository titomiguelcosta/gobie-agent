<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Application\Docker;

$docker = new Docker('tito');
$docker->run();

echo $docker->isRunning() ? 'Running' : 'Stopped';
echo $docker->getProcess()->getPid();

// sleep(3);
// echo $docker->stop() ? 'Stopped' : 'Running';

echo $docker->getProcess()->getOutput();
echo $docker->getProcess()->getErrorOutput();
sleep(20);
echo $docker->getProcess()->getOutput();
echo $docker->getProcess()->getErrorOutput();
echo $docker->destroy() ? 'Stopped' : 'Running';
