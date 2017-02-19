<?php

namespace Tools\AngularBundle\Services;

use Tools\AngularBundle\Services\FormRegistryService;

final class AngularService{
   
    public function __construct(FormRegistryService $formRegister, $formBoot ) {
        
        var_dump($formRegister->get($formBoot));
        var_dump($formRegister->get($formBoot));
    }
     
}
