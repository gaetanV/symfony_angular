<?php

namespace JsBundle\Component\Deployer\Types;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use JsBundle\Component\Translator\TranslatorInterface;
use JsBundle\Component\Entity\EntityReflection;

final class Validator implements TranslatorInterface{
    
    use \JsBundle\Component\Translator\TranslatorErrorTrait;
   
    const ERROR_CONSTRAINT_NOT_FOUND = 1;

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
            $this->entities[] = new EntityReflection($entityAlias, $translator, $validator, $em);
        }

        foreach ($form["extraField"] as $field => $asserts) {
            foreach ($asserts as $assert => $options) {
                $import = "Symfony\Component\Validator\Constraints\\" . $assert;
                if (class_exists($import)) {
                    $this->imports["asserts"][$assert] = $import;
                } else {
                    throw new \Exception($this->transError(SELF::ERROR_CONSTRAINT_NOT_FOUND, array("{{ constraint }}" => $import)));
                }
            }
        }
    }

    /**
    * {@inheritdoc}
    */
    public function getTranslator(): DataCollectorTranslator {
        return $this->translator;
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
