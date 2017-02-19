<?php

namespace Tools\AngularBundle\Component\Form;

use Symfony\Component\Form\FormFactory;

class FormCollection {

    private $collection = [];

    public function __construct($formBoot) {
        $class = new $formBoot();
        $r = new \ReflectionObject($class);
        foreach ($r->getMethods() as $method) {
            if (preg_match('/Form$/', $method->name) == true) {
                array_push($this->collection, $class->{$method->name}());
            }
        }
    }

}
