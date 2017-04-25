<?php

namespace JsBundle\Component\Entity;

use Symfony\Component\Validator\Constraint;


/**
 * @reference   : http://symfony.com/doc/current/validation.html
 * @depreciated : Validator Groupe 
 * @TODO        : Recursive collection
 * @TODO        : Finish checkAsserts 
 */
class EntityCheck{
    
    private $entity;

    const ERROR_CONFIG_FILE_SIZE_MAX = "Max size {{ max_size }} is greater than php allowed configuration {{ upload_max_filesize }} please call your administrator";
    const ERROR_CONFIG_FILE_SIZE_RANGE = "Min size {{ min_size }} is greater than Max size {{ max_size }}";
    const ERROR_CONFIG_RANGE = "Min range {{ min_range }} is greater than Max range {{ max_range }}";
    const ERROR_CONFIG_TYPE = "Constraint {{ constraint }} is not valid for this type {{ type }}";
    const ERROR_CONFIG_CONSTRAINT_NOT_SUPPORTED = "Check Constraint {{ constraint }} is not yet supported please call your administrator";
    const ERROR_CONFIG_RANGE_ORM = "Max range {{ max_range }} is greater than allowed database configuration {{ length }} ";

    /**
     * @param \JsBundle\Component\Entity\EntityReflection $entity
     */
    public function __construct(EntityReflection $entity) {
         $this->entity = $entity;
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
                if (!EntityCheck::isChar($type)) {
                    throw new \Exception(strtr(SELF::ERROR_CONFIG_TYPE, array("{{ constraint }}" => $name, "{{ type }}" => $type)));
                }
                 break;
            case "Length":
                if (!EntityCheck::isChar($type)) {
                    throw new \Exception(strtr(SELF::ERROR_CONFIG_TYPE, array("{{ constraint }}" => $name, "{{ type }}" => $type)));
                }
                if ($assert->min > $assert->max) {
                    throw new \Exception(strtr(SELF::ERROR_CONFIG_RANGE, array("{{ min_range }}" => $assert->min, "{{ max_range }}" => $assert->max)));
                }
                if ($assert->max > $length) {
                    throw new \Exception(strtr(SELF::ERROR_CONFIG_RANGE_ORM, array("{{ max_range }}" => $assert->max, "{{ length }}" => $length)));
                }
                break;
            case "Range":
                if (!EntityCheck::isNumeric($type)) {
                    throw new \Exception(strtr(SELF::ERROR_CONFIG_TYPE, array("{{ constraint }}" => $name, "{{ type }}" => $type)));
                }
                if ($assert->min > $assert->max) {
                    throw new \Exception(strtr(SELF::ERROR_CONFIG_RANGE, array("{{ min_range }}" => $assert->min, "{{ max_range }}" => $assert->max)));
                }
                if ($assert->max > $length) {
                    throw new \Exception(strtr(SELF::ERROR_CONFIG_RANGE_ORM, array("{{ max_range }}" => $assert->max, "{{ length }}" => $length)));
                }
                break;
            case "Image":
            case "File":
                $upload_max_size = ini_get('upload_max_filesize');
                if ($assert->maxSize > $upload_max_size) {
                    throw new \Exception(strtr(SELF::ERROR_CONFIG_FILE_SIZE_MAX, array("{{ upload_max_filesize }}" => $upload_max_size, "{{ max_size }}" => $assert->maxSize)));
                }
                if ($assert->maxSize > $assert->minSize) {
                    throw new \Exception(strtr(SELF::ERROR_CONFIG_FILE_SIZE_RANGE, array("{{ min_size }}" => $assert->minSize, "{{ max_size }}" => $assert->maxSize)));
                }
                break;
            default :
                throw new \Exception(strtr(SELF::ERROR_CONFIG_CONSTRAINT_NOT_SUPPORTED, array('{{ constraint }}' => $name)));
        }
    }

    /**
     * @param string $propertyName
     * @throws \Exception
     */
    public function checkAsserts(string $propertyName) {
        $metadata = $this->entity->getPropertyMetaData($propertyName);
        foreach ($this->entity->getPropertyAsserts($propertyName)->constraints as $assert) {
            try {
               $this->parseCheckAssert($assert, $metadata);
            } catch (Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }
    }
    
    /**
     * @throws \Exception
     */
    public function checkAllAsserts() {
 
        foreach ($this->entity->getAsserts()->properties as $propertyName => $property) {
            try {
                $this->checkAsserts($propertyName);
            } catch (Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }
    }

}
