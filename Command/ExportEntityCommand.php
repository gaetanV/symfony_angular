<?php

namespace JsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use JsBundle\Component\Entity\EntityReflection;
use JsBundle\Component\Entity\EntityMapping;

class ExportEntityCommand extends ContainerAwareCommand {

    const TRANS_ERROR_NAMESPACE = "JsBundle.Commmand.ExportEntityCommand";

    /**
     * {@inheritdoc}
     */
    protected function configure() {
        $this
                ->setName('js:entity')
                ->setDescription('Export Entity')
                ->addArgument('entity', InputArgument::REQUIRED, 'What is your entity?')
                ->addArgument('output', InputArgument::OPTIONAL, 'Where you want to export your mapping?');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output) {

        $languages = $this->getContainer()->get('js.languages')->getAll();
        try {

            $entityInstance = new EntityReflection($input->getArgument('entity'), $this->getContainer()->get('translator'), $this->getContainer()->get('validator'), $this->getContainer()->get("doctrine")->getManager());
            $entity = new EntityMapping($entityInstance, $languages, true);
            $output->writeln(json_encode($entity->exportAllAsserts()));
        } catch (Exception $e) {
            $output->writeln($e->getMessage());
        }
        
    }

}
