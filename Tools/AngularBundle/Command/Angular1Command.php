<?php
namespace Tools\AngularBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Angular1Command extends ContainerAwareCommand{
    
    protected function configure()
    {
            $this
                ->setName('angular:angular1')
                ->setDescription('Watch Angular CLI')
                ->addArgument('bundle', InputArgument::OPTIONAL, 'What is your bundle?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {  
         $output->writeln("deploy");
    }
    
}