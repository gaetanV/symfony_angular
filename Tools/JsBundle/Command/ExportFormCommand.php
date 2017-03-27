<?php

namespace Tools\JsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tools\JsBundle\Component\Form\FormReflection;

class ExportFormCommand extends ContainerAwareCommand {
    
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
        $languages = $this->getContainer()->getParameter('_locale');

        if (!$languages) {
            //$output->writeln($this->translateError(self::ERROR_LANGUAGE_NOT_FOUND));
            return false;
        }
        if (!is_array($languages)) {
            //$output->writeln($this->translateError(self::ERROR_LANGUAGE_FORMAT));
            return false;
        }

        $form = new FormReflection($input->getArgument('form'),  $this->getContainer()->get('form.factory'),$this->getContainer()->get('form.registry'), $this->getContainer()->get('translator'), $this->getContainer()->get('validator'), $this->getContainer()->get("doctrine")->getManager(), $languages);

        $output->writeln(json_encode([
            $form->getName() => [
                "extraFields" => $form->getExtraFields(),
                "entityFields" => [
                    $form->getEntityName() => [
                        "fields" => $form->getEntityFields()
                    ]
                ],
                "children" => []
            ]
        ]));
    }

}
