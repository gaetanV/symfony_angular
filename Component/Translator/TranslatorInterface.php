<?php

namespace JsBundle\Component\Translator;

use Symfony\Component\Translation\DataCollectorTranslator;

interface TranslatorInterface {
    
    public function getTranslator(): DataCollectorTranslator;
    
}
