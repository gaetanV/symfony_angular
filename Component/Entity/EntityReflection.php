<?php

namespace JsBundle\Component\Entity;

use Doctrine\ORM\Mapping\ClassMetadata as OrmClassMetadata;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Validator\RecursiveValidator;

use Symfony\Component\Validator\Mapping\ClassMetadata as SymfonyClassMetadata;

final class EntityReflection {

    const ERROR_ENTITY_NOT_FOUND = "The entity {{ entity }} is not found";
    const ERROR_PROPERTY_NOT_FOUND = "The property {{ property }} is not found";

    private $instance;
    private $em;
    private $asserts;
    private $meta = null;
    private $entityAlias;
    
    public function __construct(string $entityAlias, RecursiveValidator $validator, EntityManager $em) {
        
        $this->entityAlias = $entityAlias;
        $this->em = $em;
  
        if (!class_exists($entityAlias)) {
            throw new \Exception(strtr(SELF::ERROR_ENTITY_NOT_FOUND, array('{{ entity }}' => $entityAlias)));
        }
        $entity = new $entityAlias();
        $this->instance = $entity;
        $this->asserts = $validator->getMetadataFor($this->instance);
        
    }

    public function getName(){
        return $this->entityAlias;
    }
    /**
    * @return SymfonyClassMetadata
    */
    public function getAsserts(): SymfonyClassMetadata {

        return $this->asserts;
    }

    /**
     * @return OrmClassMetadata
     */
    public function getMetadata(): OrmClassMetadata {
        if ($this->meta == null) {
            $this->meta = $this->em->getClassMetadata(get_class($this->instance));
        }
        return $this->meta;
    }

    /**
     * @param string $propertyName
     * @return type
     * @throws \Exception
     */
    public function getPropertyAsserts(string $propertyName) {
        if (!isset($this->asserts->properties[$propertyName])) {
            throw new \Exception(strtr(SELF::ERROR_PROPERTY_NOT_FOUND, array('{{ property }}' => $propertyName)));
        }
        return $this->asserts->properties[$propertyName];
    }

    /**
     * @param string $propertyName
     * @return array
     * @throws \Exception
     */
    public function getPropertyMetaData(string $propertyName): array {
        $response = $this->getMetadata()->fieldMappings[$propertyName];
        if (!isset($response)) {
            throw new \Exception(strtr(SELF::ERROR_PROPERTY_NOT_FOUND, array('{{ property }}' => $propertyName)));
        }
        return $response;
    }

 

}
