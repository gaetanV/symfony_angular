<?php

namespace Tools\JsBundle\Services;
use Symfony\Component\Form\Form;
use Symfony\Component\Translation\DataCollectorTranslator;

class ToolsService {

    private $translator;
    
    public function __construct(DataCollectorTranslator $translator) {
        $this->translator = $translator;
    }

    public function getErrorMessages(Form $form):array {
        $errors = array();
        foreach ($form->getErrors() as $key => $error) {
            $template = $error->getMessageTemplate();
            $parameters = $error->getMessageParameters();

            foreach ($parameters as $var => $value) {
                $template = str_replace($var, $value, $template);
            }
            $translateValidators  = $this->translator->trans($template, array(), 'validators');
            $errors[$key] = $this->translator->trans($translateValidators);
        }
        if ($form->count()) {
            foreach ($form as $child) {
                if (!$child->isValid()) {
                    $errors[$child->getName()] = $this->getErrorMessages($child);
                }
            }
        }
        return $errors;
    }
}