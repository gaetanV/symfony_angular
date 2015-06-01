<?php

namespace Tools\AngularBundle\Factory;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ModuleController extends Controller {

    public function __construct() {
        global $kernel;
        $this->setContainer($kernel->getContainer());
    }

    public function buildView($routeAngular) {

        $twig = "";
        if (array_key_exists("_controller", $routeAngular)) {
            $twig = $this->forward($routeAngular["_controller"], array());
        } else {
            if (array_key_exists("twig", $routeAngular)) {
                
               if (!array_key_exists("inject", $routeAngular)) {
                        $twig = $this->renderView($routeAngular["twig"], array());  
               }
               else {
                    $parms = array();

                    foreach ($routeAngular["inject"] as $name => $param) {
                        switch ($param["type"]) {
                            case "form":
                                $entity = new $param["entity"]();
                                $form = new $param["form"]();
                                $form = $this->createForm($form, $entity);

                                $parms[$name] = $form->createView();
                                break;
                            case "formAngular":
                                $entity = new $param["entity"]();
                                $form = new $param["form"]();
                                $form = $this->createForm($form, $entity);
                                $formView = new \Tools\AngularBundle\Form\AngularForm($form);
                                $parms[$name] = $formView->createView();
                                break;
                        }
                    }
                    $twig = $this->renderView($routeAngular["twig"], $parms);
                }
            }
        }
        return $twig;
    }

    function createFormAngular($type, $data = null, array $options = array()) { {
            /* TO DO :: BUILDER FORM */

            $form = $this->container->get('form.factory')->create($type, $data, $options);
        }
    }

}

?>