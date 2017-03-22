<?php

namespace Tools\JsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tools\JsBundle\Component\Entity\EntityMapping;

class ExportBreezeCommand extends ContainerAwareCommand {

    const ERROR_LANGUAGE_NOT_FOUND          = 1;
    const ERROR_LANGUAGE_FORMAT             = 2;
    const TRANS_ERROR_NAMESPACE             = "JsBundle.Commmand.ExportBreezeCommand";

    /**
     * {@inheritdoc}
     */
    protected function configure() {
        $this
                ->setName('js:breeze')
                ->setDescription('Deploy Javascript entity mapping')
                ->addArgument('entity', InputArgument::REQUIRED, 'What is your entity?')
                ->addArgument('output', InputArgument::OPTIONAL, 'Where you want to export your mapping?');
    }

    /**
     * @param int $id
     * @param array $params
     * @return string
     */
    private function translateError(int $id, array $params = array()): string {
        return $this->getContainer()->get('translator')->trans(self::TRANS_ERROR_NAMESPACE . "." . $id, $params, 'errors');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $languages = $this->getContainer()->getParameter('_locale');

        if (!$languages) {
            $output->writeln($this->translateError(self::ERROR_LANGUAGE_NOT_FOUND));
            return false;
        }
        if (!is_array($languages)) {
            $output->writeln($this->translateError(self::ERROR_LANGUAGE_FORMAT));
            return false;
        }

        $entity = new EntityMapping($input->getArgument('entity'), $this->getContainer()->get('validator'), $this->getContainer()->get('translator'), $languages);
        $entity->getAsserts("username", "registration");
    }

}
