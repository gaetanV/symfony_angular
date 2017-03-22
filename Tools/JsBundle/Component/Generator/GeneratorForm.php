<?php

namespace Tools\JsBundle\Component\Generator;

use Symfony\Bundle\TwigBundle\TwigEngine;
use Tools\JsBundle\Component\Generator\FormValidator;

final class GeneratorForm {

    private $templating;
    
    /**
     * @param TwigEngine $twigEngine
     */
    
    public function __construct(TwigEngine $twigEngine) {
        $this->templating = $twigEngine;
    }

    public function deploy(FormValidator $form) {
      
        $exportMappingForm = $this->templating->render("ToolsJsBundle::formMapping.js.twig", array() );
       // var_dump($exportMappingForm);
        
        $exportSymfonyForm = $this->templating->render("ToolsJsBundle::formValidator.php.twig", array(
            "classname" => "validtor",
            "namespace" => "test",
            "imports" =>   $form->getImports(),
            "entities" =>  $form->getEntities(),
         ));
        //var_dump($exportSymfonyForm);
    }

}
