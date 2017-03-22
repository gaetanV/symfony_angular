<?php

namespace Tools\JsBundle\Component\Entity;

use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Component\Validator\Constraint;

/**
 * @reference http://symfony.com/doc/current/validation.html
 */
final class EntityMapping {

    private $meta;
    private $languages;
    private $translator;

    const ERROR_PROPERTY_NOT_FOUND   = 1;
    const ERROR_GROUPE_NOT_FOUND     = 2;
    const ERROR_ENTITY_NOT_FOUND     = 3;
    
    /**
     * @param string $entityAlias
     * @param RecursiveValidator $validator
     * @param DataCollectorTranslator $translator
     * @param array $languages
     * @throws \Exception
     */
    public function __construct(string $entityAlias, RecursiveValidator $validator, DataCollectorTranslator $translator, array $languages) {
     
        if (!class_exists($entityAlias)) {
              throw new \Exception(SELF::ERROR_ENTITY_NOT_FOUND);
        }
        $entity = new $entityAlias();
        $this->meta = $validator->getMetadataFor($entity);
        $this->translator = $translator;
        $this->languages = $languages;
    }

    /**
     * 
     * @param string $messagePattern
     * @param array $param
     * @return array
     */
    private function getTranslations(string $messagePattern, array $param = array()): array {
        $response = [];
        foreach ($this->languages as $lang) {
            $this->translator->setLocale($lang);
            $response[$lang] = $this->translator->trans($messagePattern, $param, 'validators');
        }
        return $response;
    }

    /**
     * @param Constraint $assert
     * @return array
     */
    private function parseAssert(Constraint $assert): array {
        $func = new \ReflectionClass($assert);
        $type = $func->getShortName();
        $response = [];
        switch ($type) {
            case "NotBlank":
                $response[] = $this->getTranslations($assert->message);
                break;
        }
        return $response;
    }

    /**
     * @param string $propertyName
     * @param string $groupe
     * @return array
     * @throws \Exception
     */
    public function getAsserts(string $propertyName, string $groupe = null): array {

        $response = [];
        if (!isset($this->meta->properties[$propertyName])) {
            throw new \Exception(SELF::ERROR_PROPERTY_NOT_FOUND);
        }
        $property = $this->meta->properties[$propertyName];

        if ($groupe) {
            $assertsProperty = $property->constraintsByGroup[$groupe];
            if (!isset($assertsProperty)) {
                throw new \Exception(SELF::ERROR_GROUPE_NOT_FOUND);
            }
            foreach ($assertsProperty as $assert) {
                var_dump($this->parseAssert($assert));
            }
        }
        return $response;
    }

}
