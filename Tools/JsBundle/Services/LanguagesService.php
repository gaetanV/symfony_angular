<?php

namespace Tools\JsBundle\Services;

class LanguagesService {

    private $languages = [];

    /**
     * @param array $languages
     */
    public function __construct(array $languages) {
        $this->languages = $languages;
    }

    /**
     * @return array
     */
    public function getAll(): array {
        return $this->languages;
    }

}
