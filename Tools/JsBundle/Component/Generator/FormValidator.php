<?php

namespace Tools\JsBundle\Component\Generator;

use Tools\JsBundle\Component\Entity\EntityReflection;
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Doctrine\ORM\EntityManager;

final class FormValidator {

    const TRANS_ERROR_NAMESPACE = "JsBundle.Component.Entity.FormValidator";
    const ERROR_CONSTRAINT_NOT_FOUND = 1;

    private $entities = [];
    private $imports = [
        "asserts" => [],
    ];

    /**
     * @param array $form
     * @throws \Exception
     */
    public function __construct(array $form, DataCollectorTranslator $translator, RecursiveValidator $validator, EntityManager $em) {

        foreach ($form["entityField"] as $entityAlias => $fields) {

            $fields = (array) $fields;
            $entityAlias =  str_replace("/","\\",$entityAlias);
            $this->entities[]  = new EntityReflection($entityAlias, $translator, $validator, $em);

        }

        foreach ($form["extraField"] as $field => $asserts) {
            foreach ($asserts as $assert => $options) {
                $import = "Symfony\Component\Validator\Constraints\\" . $assert;
                if (class_exists($import)) {
                    $this->imports["asserts"][$assert] = $import;
                } else {
                    throw new \Exception(SELF::ERROR_CONSTRAINT_NOT_FOUND);
                }
            }
        }
    }
    
    public function getImports() {
        return $this->imports;
    }

    public function getEntities() {
        return $this->entities;
    }

}
