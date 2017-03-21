<?php

namespace Tools\AngularBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tools\AngularBundle\Component\ConfigLoader\ConfigLoaderJson;
use Tools\AngularBundle\Component\ConfigLoader\ConfigLoaderYaml;
use Tools\AngularBundle\Component\Generator\GeneratorForm;
use Tools\AngularBundle\Component\Generator\Form;

/**
* JSON  : 0.000482   <stdClass>
* YML   : 0.004324   <array>
*/

class ServiceDeployCommand extends ContainerAwareCommand {

    /**
    * {@inheritdoc}
    */
    protected function configure() {
        $this
                ->setName('angular:service')
                ->setDescription('Deploy Angular Service CLI')
                ->addArgument('bundle', InputArgument::REQUIRED, 'What is your bundle?')
                ->addArgument('type'  , InputArgument::OPTIONAL, 'What is your type?');
    }
    
    /**
    * {@inheritdoc}
    */
    protected function execute(InputInterface $input, OutputInterface $output) {
        try{
            switch (strtoupper($input->getArgument('type'))) {
                case "YAML":
                    $configLoader = new ConfigLoaderYaml($input->getArgument('bundle'), $this->getContainer()->get('file_locator'));
                    break;
                default:
                    $configLoader = new ConfigLoaderJson($input->getArgument('bundle'), $this->getContainer()->get('file_locator'));
                    break;
            }
            $formGenerator = new GeneratorForm($this->getContainer()->get('templating'));
            foreach($configLoader->getForms() as $form) {
                $formGenerator->deploy(new Form($form));
            }
            
            $output->writeln("complet");
        } catch (Exception $e) {
            $output->writeln($e->getMessage());
        }
       
     
    }

}
