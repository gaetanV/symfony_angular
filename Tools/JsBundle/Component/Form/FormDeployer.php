<?php

namespace Tools\JsBundle\Component\Form;

use Tools\JsBundle\Component\Entity\EntityMapping;
use Tools\JsBundle\Component\Form\FormReflection;
use Tools\JsBundle\Component\Translator\TranslatorInterface;
use Symfony\Component\Translation\DataCollectorTranslator;

/**
 * @TODO        : ExtraFields
 */
final class FormDeployer implements TranslatorInterface {
    
    use \Tools\JsBundle\Component\Translator\TranslatorErrorTrait;
    
    private $ownerMapping;
    private $formReflection;
    
    /**
     * @param FormReflection $formReflection
     * @param array $languages
     */
    public function __construct(FormReflection $formReflection, array $languages) {
        
        $this->formReflection = $formReflection;
        $this->ownerMapping = new EntityMapping($formReflection->getOwner(), $languages, true);
    
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslator(): DataCollectorTranslator {
        return $this->formReflection->getTranslator();
    }

    /**
     * @return array
     */
    public function build(): array {

        $entityFields = [];
        $extraFields = [];
        foreach ($this->formReflection->getOwnerFields() as $field) {
            
            $entityFields[$field] = $this->ownerMapping->exportAsserts($field);
        }
        foreach ($this->formReflection->getExtraFields() as $field) {
            $extraFields[$field] = "todo";
        }
        return [
            $this->formReflection->getName() => [
                "extraFields" => $extraFields,
                "ownerFields" => [
                    $this->formReflection->getOwner()->getName() => [
                        "fields" => $entityFields
                    ]
                ],
                "children" => []
            ]
        ];
    }

}
