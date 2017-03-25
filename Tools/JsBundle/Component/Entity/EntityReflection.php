<?php

namespace Tools\JsBundle\Component\Entity;

use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Component\Validator\Mapping\ClassMetadata as SymfonyClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadata as OrmClassMetadata;
use Doctrine\ORM\EntityManager;

final class EntityReflection {

    const TRANS_ERROR_NAMESPACE = "JsBundle.Component.Entity.EntityReflection";
    const ERROR_ENTITY_NOT_FOUND = 1;
    const ERROR_PROPERTY_NOT_FOUND = 2;

    public $translator;
    private $instance;
    private $em;
    private $asserts;
    private $meta = null;
    private $entityAlias;
    
    public function __construct(string $entityAlias, DataCollectorTranslator $translator, RecursiveValidator $validator, EntityManager $em) {
        $this->entityAlias = $entityAlias;
        $this->em = $em;
        $this->translator = $translator;
        if (!class_exists($entityAlias)) {
            throw new \Exception($this->translateError(EntityReflection::ERROR_ENTITY_NOT_FOUND, array('{{ entity }}' => $entityAlias)));
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
            throw new \Exception($this->translateError(EntityReflection::ERROR_PROPERTY_NOT_FOUND, array('{{ property }}' => $propertyName)));
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
            throw new \Exception($this->translateError(EntityReflection::ERROR_PROPERTY_NOT_FOUND, array('{{ property }}' => $propertyName)));
        }
        return $response;
    }

    /**
     * @param int $id
     * @param array $params
     * @return string
     */
    private function translateError(int $id, array $params = array()): string {
        return $this->translator->trans(EntityReflection::TRANS_ERROR_NAMESPACE . "." . $id, $params, 'errors');
    }

}
