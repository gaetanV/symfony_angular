<?php

namespace Tools\JsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tools\JsBundle\Component\ConfigLoader\ConfigLoaderJson;
use Tools\JsBundle\Component\ConfigLoader\ConfigLoaderYaml;
use Tools\JsBundle\Component\Deployer\Builder;
use Tools\JsBundle\Component\Deployer\Types\Validator;

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
            $buidler = new Builder($this->getContainer()->get('templating'),$this->getContainer()->get('js.core'));

            foreach ($configLoader->getForms() as $form) {
                
                $validator = new Validator((array) $form, $this->getContainer()->get('translator'), $this->getContainer()->get('validator'), $this->getContainer()->get("doctrine")->getManager());
                $buidler->formMapping($validator);
                $buidler->validator($validator);
                
            }

            $output->writeln("complet");
        } catch (Exception $e) {
            $output->writeln($e->getMessage());
        }
        
    }

}
