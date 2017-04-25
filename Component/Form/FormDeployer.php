<?php

namespace JsBundle\Component\Form;

use JsBundle\Component\Entity\EntityMapping;
use JsBundle\Component\Form\FormReflection;
use Symfony\Component\Translation\DataCollectorTranslator;

/**
 * @TODO        : ExtraFields
 */
final class FormDeployer {

    private $translator;
    private $ownerMapping;
    private $formReflection;

    /**
     * @param FormReflection $formReflection
     * @param array $languages
     */
    public function __construct(FormReflection $formReflection, DataCollectorTranslator $translator, array $languages) {
        $this->translator = $translator;
        $this->formReflection = $formReflection;
        $this->ownerMapping = new EntityMapping($formReflection->getOwner(), $this->translator, $languages, true);
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
