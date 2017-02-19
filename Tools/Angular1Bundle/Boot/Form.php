<?php

namespace Tools\Angular1Bundle\Boot;
use Tools\Angular1Bundle\Entity\User;


class Form {
    public function lockForm() {
        $entity = new User();
        
        /*
        $form = new form("lock_form", $entity, "registration");
        $form->setEntityField([
            "username" => [
                "type" => TextType::class,
                "option" => array(),
            ],
            "password" => [
                "type" => TextType::class,
                "option" => array(),
            ]     
        ]);
        $form->setExtraField([
            "extraField" => [
                "type" => TextType::class,
                "constraints" => array(new NotBlank())
            ]
        ]);
        return $form;
       
        */
        return true;
    }

}
