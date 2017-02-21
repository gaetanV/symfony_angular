<?php
namespace Tools\AngularBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ServiceDeployCommand extends ContainerAwareCommand{
    
    protected function configure()
    {
            $this
                ->setName('angular:service')
                ->setDescription('Deploy Angular Service CLI')
                ->addArgument('bundle', InputArgument::OPTIONAL, 'What is your bundle?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {  
         $formRegister = $this->getContainer()->get('form.register');
         $formAngularRegister = $this->getContainer()->get('form.angular.register');
         $output->writeln("deploy");
    }
    
}