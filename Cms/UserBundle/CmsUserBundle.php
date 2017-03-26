<?php

namespace Cms\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CmsUserBundle extends Bundle {
     
        
    static $admin = array(
            "module"=> array("app.user"),
            "menu" => array(
                array(
                    "title" => "user.add",
                    "path" => "#user/add",
                ), array(
                    "title" => "user.list",
                    "path" => "#user",
                )
            ),
            "js" => array(
                "Cms/UserBundle/Resources/public/*.js",
                "Cms/UserBundle/Resources/public/user/*.js"
            ),
           "route"=>array("Cms/UserBundle/Resources/public/user.route.yml")
        /*
            "route"=> array(
                array(
                    "path"=>"/user",
                    "controller"=>"userList",
                    "template"=>"CmsUserBundle:User:list.html.twig",
                ),array(
                    "path"=>"/user/:id",
                    "templateUrl"=>"/user/add",
                    "controller"=>"userAdd",
                    "injection"=>array(
                            "form"=>array(
                                "type"=>"formAngular",
                                "entity"=> "\Cms\UserBundle\Entity\User" ,
                                "class"=> "\Cms\UserBundle\Form\UserCreate" 
                            )
                    ),
                    "template"=>"CmsUserBundle:User:list.html.twig"
                ),
                       
                
            )*/
      
    );

}
