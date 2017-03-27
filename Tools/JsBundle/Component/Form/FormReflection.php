<?php

namespace Tools\JsBundle\Component\Form;

use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Translation\DataCollectorTranslator;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormRegistry;
use Tools\JsBundle\Component\Entity\EntityReflection;
use Tools\JsBundle\Component\Entity\EntityMapping;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FormReflection {

    private $instance;
    private $formAlias;
    private $languages;
    private $name;
    private $entityName;
    private $entityFields = [];

    const TRANS_ERROR_NAMESPACE = "JsBundle.Component.Form.FormReflection";
    const ERROR_FORM_NOT_FOUND = 1;
    const REQUIRED = ["data_class", "extra_fields"];
    const EXTRA = ["style", "extra_fields" , "component"];
    
    public function __construct(string $formAlias, FormFactory $formFactory, FormRegistry $formRegistry, DataCollectorTranslator $translator, RecursiveValidator $validator, EntityManager $em, array $languages) {

        $type = $formRegistry->getType($formAlias);
        $this->languages = $languages;
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
        $entity = new EntityReflection($formResolve["data_class"], $translator, $validator, $em);
        $this->entityName= $entity->getName();
        $owner = new EntityMapping($entity, $languages, true);
        $push = $type->createBuilder($formFactory, $type->getBlockPrefix(), array());
        $type->buildForm($push, $formResolve);
     
        $this->name = $formEntity->getName();
        foreach ($push->all() as $name => $field) {
            if(in_array($name,$formResolve["extra_fields"])){
                $this->extraFields[$name] = "todo";
            }else{
                $this->entityFields[$name] = $owner->exportAsserts($name);
            }
        }
    }

    public function getExtraFields() {
        return $this->extraFields;
    }
    
    public function getEntityFields() {
        return $this->entityFields;
    }

    public function getEntityName() {
        return $this->entityName;
    }

    public function getName() {
        return $this->name;
    }

    public function getChildren() {
        
    }

}
