<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use Builder\Command\DeployCommand;
use Symfony\Component\Console\Application;

$application = new Application();

$application->add(new DeployCommand());
$application->run();