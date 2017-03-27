<?php

namespace Tools\JsBundle\Component\Entity;

use Doctrine\ORM\Mapping\ClassMetadata as OrmClassMetadata;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Component\Validator\Mapping\ClassMetadata as SymfonyClassMetadata;

use Tools\JsBundle\Component\Translator\TranslatorInterface;


final class EntityReflection implements TranslatorInterface {

    use \Tools\JsBundle\Component\Translator\TranslatorErrorTrait;
    
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
            throw new \Exception($this->transError(SELF::ERROR_ENTITY_NOT_FOUND, array('{{ entity }}' => $entityAlias)));
        }
        $entity = new $entityAlias();
        $this->instance = $entity;
        $this->asserts = $validator->getMetadataFor($this->instance);
        
    }
    
    /**
    * {@inheritdoc}
    */
    public function getTranslator(): DataCollectorTranslator {
        return $this->translator;
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
            throw new \Exception($this->transError(SELF::ERROR_PROPERTY_NOT_FOUND, array('{{ property }}' => $propertyName)));
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
            throw new \Exception($this->transError(SELF::ERROR_PROPERTY_NOT_FOUND, array('{{ property }}' => $propertyName)));
        }
        return $response;
    }

 

}
