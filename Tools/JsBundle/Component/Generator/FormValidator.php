<?php

namespace Tools\JsBundle\Component\Generator;



final class FormValidator {

    /**
     * @TODO Translator : ERROR
     */
    const ERROR_ASSET_NOT_FOUND  = 1;
    const ERROR_ENTITY_NOT_FOUND = 2;

    private $entities = [];
    private $imports = [
        "asserts" => [],
    ];
    
    /**
     * @param array $form
     * @throws \Exception
     */
    public function __construct(array $form) {
       
        foreach ($form["entityField"] as $entity => $fields) {
            
            $fields = (array)$fields;
            $import =  str_replace("/","\\",$entity);
            if (class_exists($import)) {
                $groupe = false;
                if (isset($fields["groupe"])) {
                    $groupe = $fields["groupe"];
                }
                $this->entities[] = ["name" => "\\".$import, "groupe" => $groupe];
                
            } else {
                throw new \Exception(SELF::ERROR_ENTITY_NOT_FOUND);
            }
        }

        foreach ($form["extraField"] as $field => $asserts) {
            foreach ($asserts as $assert => $options) {
                $import = "Symfony\Component\Validator\Constraints\\" . $assert;
                if (class_exists($import)) {
                    $this->imports["asserts"][$assert] = $import;
                } else {
                    throw new \Exception(SELF::ERROR_ASSET_NOT_FOUND);
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
