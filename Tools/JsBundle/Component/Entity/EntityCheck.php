<?php

namespace Tools\JsBundle\Component\Entity;

use Symfony\Component\Validator\Constraint;

/**
 * @reference   : http://symfony.com/doc/current/validation.html
 * @depreciated : Validator Groupe 
 * @TODO        : Recursive collection
 * @TODO        : Finish checkAsserts 
 */
class EntityCheck {
    
    private $entity;

    const TRANS_ERROR_NAMESPACE = "JsBundle.Component.Entity.EntityCheck";

    const ERROR_CONFIG_FILE_SIZE_MAX = 1;
    const ERROR_CONFIG_FILE_SIZE_RANGE = 2;
    const ERROR_CONFIG_RANGE = 3;
    const ERROR_CONFIG_TYPE = 4;
    const ERROR_CONFIG_CONSTRAINT_NOT_SUPPORTED = 5;
    const ERROR_CONFIG_RANGE_ORM = 6;

    /**
     * @param \Tools\JsBundle\Component\Entity\EntityReflection $entity
     */
    public function __construct(EntityReflection $entity) {
         $this->entity = $entity;
    }

    /**
     * @param int $id
     * @param array $params
     * @return string
     */
    private function translateError(int $id, array $params = array()): string {
        return $this->entity->translator->trans(EntityCheck::TRANS_ERROR_NAMESPACE . "." . $id, $params, 'errors');
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
                    throw new \Exception($this->translateError(EntityCheck::ERROR_CONFIG_TYPE, array("{{ constraint }}" => $name, "{{ type }}" => $type)));
                }
                 break;
            case "Length":
                if (!EntityCheck::isChar($type)) {
                    throw new \Exception($this->translateError(EntityCheck::ERROR_CONFIG_TYPE, array("{{ constraint }}" => $name, "{{ type }}" => $type)));
                }
                if ($assert->min > $assert->max) {
                    throw new \Exception($this->translateError(EntityCheck::ERROR_CONFIG_RANGE, array("{{ min_range }}" => $assert->min, "{{ max_range }}" => $assert->max)));
                }
                if ($assert->max > $length) {
                    throw new \Exception($this->translateError(EntityCheck::ERROR_CONFIG_RANGE_ORM, array("{{ max_range }}" => $assert->max, "{{ length }}" => $length)));
                }
                break;
            case "Range":
                if (!EntityCheck::isNumeric($type)) {
                    throw new \Exception($this->translateError(EntityCheck::ERROR_CONFIG_TYPE, array("{{ constraint }}" => $name, "{{ type }}" => $type)));
                }
                if ($assert->min > $assert->max) {
                    throw new \Exception($this->translateError(EntityCheck::ERROR_CONFIG_RANGE, array("{{ min_range }}" => $assert->min, "{{ max_range }}" => $assert->max)));
                }
                if ($assert->max > $length) {
                    throw new \Exception($this->translateError(EntityCheck::ERROR_CONFIG_RANGE_ORM, array("{{ max_range }}" => $assert->max, "{{ length }}" => $length)));
                }
                break;
            case "Image":
            case "File":
                $upload_max_size = ini_get('upload_max_filesize');
                if ($assert->maxSize > $upload_max_size) {
                    throw new \Exception($this->translateError(EntityCheck::ERROR_CONFIG_FILE_SIZE_MAX, array("{{ upload_max_filesize }}" => $upload_max_size, "{{ max_size }}" => $assert->maxSize)));
                }
                if ($assert->maxSize > $assert->minSize) {
                    throw new \Exception($this->translateError(EntityCheck::ERROR_CONFIG_FILE_SIZE_RANGE, array("{{ min_size }}" => $assert->minSize, "{{ max_size }}" => $assert->maxSize)));
                }
                break;
            default :
                throw new \Exception($this->translateError(EntityCheck::ERROR_CONFIG_CONSTRAINT_NOT_SUPPORTED, array('{{ constraint }}' => $name)));
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
