<?php

namespace Tools\AngularBundle\Services;
use \Symfony\Component\DependencyInjection\ContainerAware;

class AngularService extends ContainerAware {

    public function createFormView($form) {
        $formView = $form->createView();
        $this->container->get('twig')->getExtension('form')->renderer->setTheme($formView, "ToolsAngularBundle::fields.html.twig");
        $validator= $this->container->get('validator');
       // $validator = new \Symfony\Component\Validator\Mapping\ClassMetadataFactory();
        $propertyMetadata = $validator->getMetadataFor($form->getData());
        $this->getProperty($formView, $propertyMetadata, $form);
        return $formView;
    }

    private function getProperty($formView, $metadata, $form) {
        foreach ($formView->children as $key => $value) {
            if (!isset($value->constraints))
                $value->constraints = array();
            /* METADATA PROPERTY */
            if ($metadata->hasPropertyMetadata($key)) {
                $propertyMetadata = $metadata->getPropertyMetadata($key);
                foreach ($propertyMetadata[0]->constraints as $constraint) {
                    $function = new \ReflectionClass($constraint);
                    $name = $function->getShortName();
                    if (!array_key_exists($name, $value->constraints)) {$value->constraints[$name] = array();}
                    array_push($value->constraints[$name], $constraint);
                }
            }

            /* OFFSET FORM */
            if ($form->offsetExists($key)) {
                $config = $form->offsetGet($key)->getConfig();
                $function = new \ReflectionClass($config->getType()->getInnerType());
                $type = $function->getShortName();
                /* MATCH */
                if ($type === "RepeatedType") {
                    $option = $config->getAttribute("data_collector/passed_options");
                    $error = $option["invalid_message"];
                    foreach ($value->children as $keyChild => $valueChild) {
                        $valueChild->constraints["Match"] = array();
                        $valueChild->constraints["Match"][0] = array("message" => $error, "match" => "[" . $value->vars["name"] . "][first]");
                    }
                    $value->children["first"]->constraints = $value->constraints;
                }
            }
            /* CHILD
              if(count($value->children)>0){
              $this->getProperty($value,$metadata,$form);
              } */
        }
    }

}
