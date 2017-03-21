<?php

namespace Tools\AngularBundle\Component\Generator;

use Symfony\Bundle\TwigBundle\TwigEngine;
use Tools\AngularBundle\Component\Generator\FormValidator;

final class GeneratorForm {

    private $templating;

    public function __construct(TwigEngine $twigEngine) {
        $this->templating = $twigEngine;
    }

    public function deploy(FormValidator $form) {
       
        $exportMappingForm = $this->templating->render("ToolsAngularBundle::formMapping.js.twig", array() );
        var_dump($exportMappingForm);
        
        $exportSymfonyForm = $this->templating->render("ToolsAngularBundle::formValidator.php.twig", array(
            "classname" => "validtor",
            "namespace" => "test",
            "imports" =>   $form->getImports(),
            "entities" =>  $form->getEntities(),
         ));
        var_dump($exportSymfonyForm);
    }

}
