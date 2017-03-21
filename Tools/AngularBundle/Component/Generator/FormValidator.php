<?php

namespace Tools\AngularBundle\Component\Generator;


final class FormValidator {

    /**
     * @TODO Translator : ERROR
     */
    const ERROR_ASSET_NOT_FOUND  = 3;
    const ERROR_ENTITY_NOT_FOUND = 4;

    private $entities = [];
    private $imports = [
        "asserts" => [],
        "entities" => [],
    ];

    public function __construct(array $form) {
       
        foreach ($form["entityField"] as $entity => $fields) {
            
            $fields = (array)$fields;
            $import =  str_replace("/","\\",$entity);
            if (class_exists($import)) {
                
                $this->imports["entities"][$entity] = $import;
                $groupe = false;
                if (isset($fields["groupe"])) {
                    $groupe = $fields["groupe"];
                }
                $this->entities[] = ["name" => $entity, "groupe" => $groupe];
                
            } else {
                throw new \Exception(SELF::ERROR_ENTITY_NOT_FOUND);
            }
        }

        foreach ($form["extraField"] as $field => $asserts) {
            foreach ($asserts as $asset => $options) {
                $import = "Symfony\Component\Validator\Constraints\\" . $asset;
                if (class_exists($import)) {
                    $this->imports["asserts"][$asset] = $import;
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
