<?php

declare(strict_types=1);

namespace Builder\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

class DeployCommand extends Command {
 
    protected function configure() {
        $this
                ->setName('pom:deploy')
                ->setDescription('Deploy Javascript Services & Validator')
                ->addArgument('pom', InputArgument::REQUIRED, 'Where is your file?');
    }
   
    protected function execute(InputInterface $input, OutputInterface $output) {

    }
}