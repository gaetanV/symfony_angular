<?php

namespace Tools\JsBundle\Services;
use Symfony\Component\Form\Form;
use Symfony\Component\Translation\DataCollectorTranslator;

class ToolsService {

    private $translator;
    
    public function __construct(DataCollectorTranslator $translator) {
        $this->translator = $translator;
    }

    public function getErrorMessages(Form $form) {
            //ERROR
    }
}