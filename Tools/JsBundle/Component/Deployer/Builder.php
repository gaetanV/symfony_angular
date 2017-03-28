<?php

namespace Tools\JsBundle\Component\Deployer;

use Symfony\Bundle\TwigBundle\TwigEngine;
use Tools\JsBundle\Component\Deployer\Types\Validator;
use Tools\JsBundle\Services\CoreInformationService;

final class Builder {

    const TEMPLATE_PHP_VALIDATOR = "ToolsJsBundle::Validator.php.twig";
    const TEMPLATE_JS_FORM = "ToolsJsBundle::FormMapping.js.twig";

    private $templating;
    private $coreInformation;

    /**
     * @param TwigEngine $twigEngine
     */
    public function __construct(TwigEngine $twigEngine, CoreInformationService $coreInformation) {
        $this->coreInformation = $coreInformation;
        $this->templating = $twigEngine;
    }

    /**
     * @param FormValidator $form
     */
    public function validator(Validator $form) {

        $exportSymfonyForm = $this->templating->render(SELF::TEMPLATE_PHP_VALIDATOR, array(
            "classname" => "validtor",
            "namespace" => "test",
            "imports" => $form->getImports(),
            "entities" => $form->getEntities(),
            "version" => $this->coreInformation->version(),
            "security" => $this->coreInformation->certifiction(2),
            "symfony_version" => $this->coreInformation->symfonyVersion(),
            "date" => $this->coreInformation->date()
        ));
        var_dump($exportSymfonyForm);
    }

    public function formMapping(Validator $form) {

        $exportMappingForm = $this->templating->render(SELF::TEMPLATE_JS_FORM, array(
            "version" => $this->coreInformation->version(),
            "security" => $this->coreInformation->certifiction(2),
            "symfony_version" => $this->coreInformation->symfonyVersion(),
            "date" => $this->coreInformation->date()
        ));
        var_dump($exportMappingForm);
    }

}
