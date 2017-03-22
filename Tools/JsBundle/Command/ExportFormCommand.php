<?php

namespace Tools\JsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportFormCommand extends ContainerAwareCommand {

    /**
     * {@inheritdoc}
     */
    protected function configure() {
        $this
                ->setName('js:form')
                ->setDescription('Deploy Javascript form mapping')
                ->addArgument('form', InputArgument::REQUIRED, 'What is your form?')
                ->addArgument('output', InputArgument::OPTIONAL, 'Where you want to export your mapping?');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output) {

     
    }

}
