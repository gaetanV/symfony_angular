<?php

namespace Tools\JsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tools\JsBundle\Component\Form\FormReflection;
use Tools\JsBundle\Component\Form\FormDeployer;

class ExportFormCommand extends ContainerAwareCommand  {
    
   
    /**
     * {@inheritdoc}
     */
    protected function configure() {
        $this
                ->setName('js:form')
                ->setDescription('Export Form')
                ->addArgument('form', InputArgument::REQUIRED, 'What is your form?')
                ->addArgument('output', InputArgument::OPTIONAL, 'Where you want to export your mapping?');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
   
        $languages = $this->getContainer()->get('js.languages')->getAll();
        $form = new FormReflection($input->getArgument('form'), $this->getContainer()->get('form.factory'), $this->getContainer()->get('form.registry'), $this->getContainer()->get('translator'), $this->getContainer()->get('validator'), $this->getContainer()->get("doctrine")->getManager());
        $formDeployer = new FormDeployer($form, $languages);
        $output->writeln(json_encode($formDeployer->build()));
    }

}
