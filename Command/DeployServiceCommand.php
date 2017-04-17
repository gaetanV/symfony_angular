<?php

namespace JsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use JsBundle\Component\Deployer\Builder;
use JsBundle\Component\Deployer\Types\Validator;

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
        $kernel = $this->getContainer()->get('kernel');
        $bundle = $kernel->getBundle($input->getArgument('bundle'));
        $bundle->shutdown();
        $extension = $bundle->getContainerExtension();
        try {
            $buidler = new Builder($this->getContainer()->get('templating'), $this->getContainer()->get('js.core'));
            foreach ($extension->deployForms() as $form) {

                $validator = new Validator((array) $form, $this->getContainer()->get('translator'), $this->getContainer()->get('validator'), $this->getContainer()->get("doctrine")->getManager());
                $buidler->formMapping($validator);
                $buidler->validator($validator);
            }

            $output->writeln("complet");
        } catch (Exception $e) {
            $output->writeln($e->getMessage());
        }
        $bundle->boot();
    }

}
