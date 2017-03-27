<?php

namespace Tools\JsBundle\Component\Form;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormRegistry;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Tools\JsBundle\Component\Translator\TranslatorInterface;
use Tools\JsBundle\Component\Entity\EntityReflection;

/**
 * @TODO        : Children
 */
final class FormReflection implements TranslatorInterface {

    use \Tools\JsBundle\Component\Translator\TranslatorErrorTrait;

    const ERROR_PROPERTY_NOT_FOUND = 1;

    private $instance;
    private $formAlias;
    private $name;
    private $owner;
    private $entityFields = [];
    private $extraFields = [];
    public $translator;

    const REQUIRED = ["data_class"];

    /**
     * @param string $formAlias
     * @param FormFactory $formFactory
     * @param FormRegistry $formRegistry
     * @param DataCollectorTranslator $translator
     * @param RecursiveValidator $validator
     * @param EntityManager $em
     */
    public function __construct(string $formAlias, FormFactory $formFactory, FormRegistry $formRegistry, DataCollectorTranslator $translator, RecursiveValidator $validator, EntityManager $em) {

        $this->translator = $translator;
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
        $this->owner = new EntityReflection($formResolve["data_class"], $translator, $validator, $em);


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
                    $this->trans(self::ERROR_PROPERTY_NOT_FOUND, array('{{ property }}' => $propertyName));
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
