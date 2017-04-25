<?php

namespace JsBundle\Component\Deployer\Types;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Component\Validator\Validator\RecursiveValidator;

use JsBundle\Component\Entity\EntityReflection;

final class Validator  {
    

   
    const ERROR_CONSTRAINT_NOT_FOUND = "The Constraint {{ constraint }} is not found";

    private $entities = [];
    private $imports = [
        "asserts" => [],
    ];
    private $translator;

    /**
     * @param array $form
     * @throws \Exception
     */
    public function __construct(array $form, DataCollectorTranslator $translator, RecursiveValidator $validator, EntityManager $em) {
           
        $this->translator = $translator;
        foreach ($form["entityField"] as $entityAlias => $fields) {

            $fields = (array) $fields;
            $entityAlias = str_replace("/", "\\", $entityAlias);
            $this->entities[] = new EntityReflection($entityAlias, $validator, $em);
        }

        foreach ($form["extraField"] as $field => $asserts) {
            foreach ($asserts as $assert => $options) {
                $import = "Symfony\Component\Validator\Constraints\\" . $assert;
                if (class_exists($import)) {
                    $this->imports["asserts"][$assert] = $import;
                } else {
                    throw new \Exception(strtr(SELF::ERROR_CONSTRAINT_NOT_FOUND, array("{{ constraint }}" => $import)));
                }
            }
        }
    }


    
    /**
     * @return array
     */
    public function getImports(): array {
        return $this->imports;
    }

    /**
     * @return array
     */
    public function getEntities(): array {
        return $this->entities;
    }

}
