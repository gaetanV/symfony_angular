<?php

namespace Tools\JsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tools\JsBundle\Component\ConfigLoader\ConfigLoaderJson;
use Tools\JsBundle\Component\ConfigLoader\ConfigLoaderYaml;
use Tools\JsBundle\Component\Generator\GeneratorForm;
use Tools\JsBundle\Component\Generator\FormValidator;

class DeployServiceCommand extends ContainerAwareCommand {

    /**
     * {@inheritdoc}
     */
    protected function configure() {
        $this
                ->setName('js:service')
                ->setDescription('Deploy Javascript Services & Validator')
                ->addArgument('bundle', InputArgument::REQUIRED, 'What is your bundle?')
                ->addArgument('type', InputArgument::OPTIONAL, 'What is your type?');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
   
        try {
            switch (strtoupper($input->getArgument('type'))) {
                case "YAML":
                    $configLoader = new ConfigLoaderYaml($input->getArgument('bundle'), $this->getContainer()->get('file_locator'));
                    break;
                default:
                    $configLoader = new ConfigLoaderJson($input->getArgument('bundle'), $this->getContainer()->get('file_locator'));
                    break;
            }
            $formGenerator = new GeneratorForm($this->getContainer()->get('templating'),$this->getContainer()->get('validator'));
            foreach ($configLoader->getForms() as $form) {
                $formGenerator->deploy(new FormValidator((array)$form));
            }

            $output->writeln("complet");
        } catch (Exception $e) {
            $output->writeln($e->getMessage());
        }
    }

}
