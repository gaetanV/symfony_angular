<?php

namespace Tools\JsBundle\Component\Entity;

use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Component\Validator\Constraint;
use Doctrine\ORM\EntityManager;

/**
 * @reference   : http://symfony.com/doc/current/validation.html
 * @TODO        : Recursive collection
 * @TODO        : Finish checkAsserts 
 * @depreciated : Validator Groupe 
 */
final class EntityMappingCheck {
    
    private $metadata;
    private $meta;
    private $translator;

    const TRANS_ERROR_NAMESPACE = "JsBundle.Component.Entity.EntityMappingCheck";

    const ERROR_CONFIG_FILE_SIZE_MAX = 1;
    const ERROR_CONFIG_FILE_SIZE_RANGE = 2;
    const ERROR_CONFIG_RANGE = 3;
    const ERROR_CONFIG_TYPE = 4;
    const ERROR_CONFIG_CONSTRAINT_NOT_SUPPORTED = 5;
    const ERROR_CONFIG_RANGE_ORM = 6;
    const ERROR_ENTITY_NOT_FOUND = 7;


    /**
     * @param string $entityAlias
     * @param RecursiveValidator $validator
     * @param DataCollectorTranslator $translator
     * @param array $languages
     * @throws \Exception
     */
    public function __construct(string $entityAlias, RecursiveValidator $validator, DataCollectorTranslator $translator, EntityManager $em) {

        if (!class_exists($entityAlias)) {
            throw new \Exception($this->translateError(SELF::ERROR_ENTITY_NOT_FOUND, array('{{ entity }}' => $entityAlias)));
        }
        $entity = new $entityAlias();
        $this->metadata = $em->getClassMetadata($entityAlias);
        $this->meta = $validator->getMetadataFor($entity);
        $this->translator = $translator;
    }

    /**
     * @param int $id
     * @param array $params
     * @return string
     */
    private function translateError(int $id, array $params = array()): string {
        return $this->translator->trans(SELF::TRANS_ERROR_NAMESPACE . "." . $id, $params, 'errors');
    }

    /**
     * @param string $type
     * @return bool
     */
    static function isChar(string $type):bool {
        return $type === "string" || $type === "text";
    }
    
    /**
     * @param string $type
     * @return bool
     */
    static function isNumeric(string $type):bool {
        return $type === "integer" || $type === "smallint" || $type === "bigint" || $type === "decimal" || $type === "float";
    }

   /**
    * 
    * @param Constraint $assert
    * @param array $orm
    * @throws \Exception
    */
    private function parseCheckAssert(Constraint $assert, array $orm) {

        $type    = $orm["type"];
        $length  = $orm["length"];
        
        $func = new \ReflectionClass($assert);

        $name = $func->getShortName();
        switch ($name) {
            case "NotBlank":
            case "Blank":
            case "Regex":
                break;
            case "Url":
            case "Email":
                if (!SELF::isChar($type)) {
                    throw new \Exception($this->translateError(self::ERROR_CONFIG_TYPE, array("{{ constraint }}" => $name, "{{ type }}" => $type)));
                }
                 break;
            case "Length":
                if (!SELF::isChar($type)) {
                    throw new \Exception($this->translateError(self::ERROR_CONFIG_TYPE, array("{{ constraint }}" => $name, "{{ type }}" => $type)));
                }
                if ($assert->min > $assert->max) {
                    throw new \Exception($this->translateError(self::ERROR_CONFIG_RANGE, array("{{ min_range }}" => $assert->min, "{{ max_range }}" => $assert->max)));
                }
                if ($assert->max > $length) {
                    throw new \Exception($this->translateError(self::ERROR_CONFIG_RANGE_ORM, array("{{ max_range }}" => $assert->max, "{{ length }}" => $length)));
                }
                break;
            case "Range":
                if (!SELF::isNumeric($type)) {
                    throw new \Exception($this->translateError(self::ERROR_CONFIG_TYPE, array("{{ constraint }}" => $name, "{{ type }}" => $type)));
                }
                if ($assert->min > $assert->max) {
                    throw new \Exception($this->translateError(self::ERROR_CONFIG_RANGE, array("{{ min_range }}" => $assert->min, "{{ max_range }}" => $assert->max)));
                }
                if ($assert->max > $length) {
                    throw new \Exception($this->translateError(self::ERROR_CONFIG_RANGE_ORM, array("{{ max_range }}" => $assert->max, "{{ length }}" => $length)));
                }
                break;
            case "Image":
            case "File":
                $upload_max_size = ini_get('upload_max_filesize');
                if ($assert->maxSize > $upload_max_size) {
                    throw new \Exception($this->translateError(self::ERROR_CONFIG_FILE_SIZE_MAX, array("{{ upload_max_filesize }}" => $upload_max_size, "{{ max_size }}" => $assert->maxSize)));
                }
                if ($assert->maxSize > $assert->minSize) {
                    throw new \Exception($this->translateError(self::ERROR_CONFIG_FILE_SIZE_RANGE, array("{{ min_size }}" => $assert->minSize, "{{ max_size }}" => $assert->maxSize)));
                }
                break;
            default :
                throw new \Exception($this->translateError(SELF::ERROR_CONFIG_CONSTRAINT_NOT_SUPPORTED, array('{{ constraint }}' => $name)));
        }
    }

    /**
     * @param string $propertyName
     * @throws \Exception
     */
    public function checkAssert(string $propertyName) {

        if (!isset($this->metadata->fieldMappings[$propertyName])) {
            throw new \Exception($this->translateError(SELF::ERROR_PROPERTY_NOT_FOUND, array('{{ property }}' => $propertyName)));
        }
        if (!isset($this->meta->properties[$propertyName])) {
            throw new \Exception($this->translateError(SELF::ERROR_PROPERTY_NOT_FOUND, array('{{ property }}' => $propertyName)));
        }
        $property = $this->meta->properties[$propertyName];

        $orm = $this->metadata->fieldMappings[$propertyName];

        foreach ($property->constraints as $assert) {
            try {

                $this->parseCheckAssert($assert, $orm);
            } catch (Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }
    }

    public function checkAllAsserts() {
        foreach ($this->meta->properties as $propertyName => $property) {
            try {
                $this->checkAssert($propertyName);
            } catch (Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }
    }

}
