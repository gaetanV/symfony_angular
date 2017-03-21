<?php

namespace Tools\AngularBundle\Component\Generator;

use Symfony\Bundle\TwigBundle\TwigEngine;
use Tools\AngularBundle\Component\Generator\Form;

final class GeneratorForm {

    private $templating;

    public function __construct(TwigEngine $twigEngine) {
        $this->templating = $twigEngine;
    }

    public function deploy(Form $form) {

        $phpFile = $this->templating->render("ToolsAngularBundle::form.php.twig", array("form" => $form));
        var_dump($phpFile);
    }

}
