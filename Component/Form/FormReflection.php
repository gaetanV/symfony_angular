<?php

namespace JsBundle\Component\Form;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormRegistry;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use JsBundle\Component\Entity\EntityReflection;

/**
 * @TODO        : Children
 */
final class FormReflection {



    const ERROR_PROPERTY_NOT_FOUND = "The property {{ property }} is not found";

    private $instance;
    private $formAlias;
    private $name;
    private $owner;
    private $entityFields = [];
    private $extraFields = [];


    const REQUIRED = ["data_class"];

    /**
     * @param string $formAlias
     * @param FormFactory $formFactory
     * @param FormRegistry $formRegistry
     * @param RecursiveValidator $validator
     * @param EntityManager $em
     */
    public function __construct(string $formAlias, FormFactory $formFactory, FormRegistry $formRegistry,  RecursiveValidator $validator, EntityManager $em) {

       
        $type = $formRegistry->getType($formAlias);
        $this->formAlias = $formAlias;
        $this->instance = $type;
        $formEntity = new $formAlias();
        $optionFormResolve = $type->getOptionsResolver();
        $optionFormResolve->setRequired(SELF::REQUIRED);
        $optionFormResolve->setDefault("extra_fields", array());
        $optionFormResolve->setDefault("style", false);
        $optionFormResolve->setDefault("component", false);
        $formEntity->setDefaultOptions($optionFormResolve);
        $formResolve = $optionFormResolve->resolve();
        $this->owner = new EntityReflection($formResolve["data_class"], $validator, $em);
        $push = $type->createBuilder($formFactory, $type->getBlockPrefix(), array());
        $type->buildForm($push, $formResolve);

        $this->name = $formEntity->getName();
        foreach ($push->all() as $name => $field) {
            if (in_array($name, $formResolve["extra_fields"])) {
                $this->extraFields[] = $name;
            } else {
                if ($this->owner->getPropertyMetaData($name)) {
                    $this->entityFields[] = $name;
                } else {
                   throw new \Exception(strtr(self::ERROR_PROPERTY_NOT_FOUND, array('{{ property }}' => $propertyName)));
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getExtraFields(): array {
        return $this->extraFields;
    }

    /**
     * @return array
     */
    public function getOwnerFields(): array {
        return $this->entityFields;
    }

    /**
     * @return EntityReflection
     */
    public function getOwner(): EntityReflection {
        return $this->owner;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    public function getChildren() {
        
    }

}
