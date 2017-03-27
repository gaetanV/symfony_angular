<?php

namespace Tools\JsBundle\Component\Translator;

use Symfony\Component\Translation\DataCollectorTranslator;

interface TranslatorInterface {
    
    public function getTranslator(): DataCollectorTranslator;
    
}
