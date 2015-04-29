<?php

namespace Tools\AngularBundle\Services;
use \Symfony\Component\DependencyInjection\ContainerAware;
use \Symfony\Component\Translation\Loader\XliffFileLoader; 

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
                 $translator=  $this->container->get('translator');
                 
    
        foreach ($formView->children as $key => $value) {
            if (!isset($value->constraints))
                $value->constraints = array();
            /* METADATA PROPERTY */
            if ($metadata->hasPropertyMetadata($key)) {
                $propertyMetadata = $metadata->getPropertyMetadata($key);
                
                foreach ($propertyMetadata[0]->constraints as $constraint) {
                    $function = new \ReflectionClass($constraint);
                    $name = $function->getShortName();
                  
                  
                    if(property_exists($constraint, "maxMessage")){
                        
                              $t =  $translator->trans($constraint->maxMessage, array(), 'validators'); 
                              $t =  $translator->trans($t); 
                               $t= \preg_replace("/{{ limit }}/",$constraint->max,$t);
                              $constraint->maxMessage=$t;
                    }
                     if(property_exists($constraint, "minMessage")){
                              $t =  $translator->trans($constraint->minMessage, array(), 'validators'); 
                              $t =  $translator->trans($t); 
                               $t= \preg_replace("/{{ limit }}/",$constraint->min,$t);
                              $constraint->minMessage=$t;
                    }
                    
                    if(property_exists($constraint, "message")){
                                 //TO DO TRANSFERT DOMAIN
                                 $t =  $translator->trans($constraint->message, array(), 'validators'); 
                                 $t =  $translator->trans($t); 
                                 $constraint->message=$t;
                       }
                    
                    
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
