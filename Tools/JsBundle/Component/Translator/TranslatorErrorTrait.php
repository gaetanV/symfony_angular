<?php

namespace Tools\JsBundle\Component\Translator;

trait TranslatorErrorTrait {
    
    /**
     * @param string $id
     * @param type $params
     * @return string
     */
    private function transError(int $id, $params = array()): string {
        return $this->getTranslator()->trans(SELF::class . "." . $id, $params, 'errors');
    }
    
    /**
     * @param string $id
     * @param int $number
     * @param array $param
     * @return string
     */
    private function transErrorChoice(int $id, int $number, array $param = array()): string {
        return $this->getTranslator()->transChoice(SELF::class . "." . $id, $number, $param, 'errors');
    }

}
