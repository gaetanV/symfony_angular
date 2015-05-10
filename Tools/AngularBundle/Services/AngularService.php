<?php

namespace Tools\AngularBundle\Services;

use \Symfony\Component\DependencyInjection\ContainerAware;
use \Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class AngularService extends ContainerAware {

    public function getErrorMessages(\Symfony\Component\Form\Form $form) {
        $translator = $this->container->get('translator');
        $errors = array();
        foreach ($form->getErrors() as $key => $error) {
            $template = $error->getMessageTemplate();
            $parameters = $error->getMessageParameters();

            foreach ($parameters as $var => $value) {
                $template = str_replace($var, $value, $template);
            }
            $t = $translator->trans($template, array(), 'validators');
            $t = $translator->trans($t);
            $errors[$key] = $t;
        }
        if ($form->count()) {
            foreach ($form as $child) {
                if (!$child->isValid()) {
                    $errors[$child->getName()] = $this->getErrorMessages($child);
                }
            }
        }
        return $errors;
    }
    
 public function replace_tmp_file($form )
    {
     
        if(is_array($form)){
           foreach ($form as $key=>$field){
                  if(is_array($field)){
                        if(array_key_exists("tmp_file",$field)) {
                                   $img=  $form[$key]["tmp_file"]["image"];
                                  	$img = str_replace('data:'.$form[$key]["tmp_file"]["type"].';base64,', '', $img);
                                    $img = str_replace(' ', '+', $img); 
                                    
                                    $nameTemp=sha1(uniqid(mt_rand(), true));
                                    $tmpDirectory=sys_get_temp_dir()."/".$nameTemp;
                                    
                                   if( file_put_contents($tmpDirectory,base64_decode($img))){
                                       $form[$key]=$tmpDirectory;
                                     //  $form[$key]=new \Symfony\Component\HttpFoundation\File\UploadedFile($tmpDirectory,$nameTemp);
 
                                   };
                        }

                   }
            }
            
        }
        return $form;
    }
    
    public function jsonToRequest($request) {
        $jsonData = json_decode($request->getContent(), true);
         ///TRANFORM IMAGE TO FILE
          foreach ($jsonData as $key=>$form){
                 $jsonData[$key]=$this->replace_tmp_file($form);
          }
        $request->request->replace($jsonData);
        return $request;
    }

    public function createFormView($form) {
        $formView = $form->createView();
        $this->container->get('twig')->getExtension('form')->renderer->setTheme($formView, "ToolsAngularBundle::fields.html.twig");
        $validator = $this->container->get('validator');
        $propertyMetadata = $validator->getMetadataFor($form->getData());
        $this->getProperty($formView, $propertyMetadata, $form);
        return $formView;
    }

    private function getProperty($formView, $metadataForm, $form) {
        $translator = $this->container->get('translator');

        foreach ($formView->children as $key => $value) {
            $metadata=$metadataForm;
            
            if (!isset($value->constraints))
                $value->constraints = array();
            /* METADATA PROPERTY */
            
                
            if(is_object($value->vars["value"])){
              
                  $validator = $this->container->get('validator');
                  $metadata = $validator->getMetadataFor($value->vars["value"]);
     
           }
         
                 
           
            if ($metadata->hasPropertyMetadata($key)) {
                $propertyMetadata = $metadata->getPropertyMetadata($key);
                foreach ($propertyMetadata[0]->constraints as $constraint) {
                    $function = new \ReflectionClass($constraint);
                    $name = $function->getShortName();
                    $this->translateConstraintErrors($constraint);
                    if (!array_key_exists($name, $value->constraints)) {
                        $value->constraints[$name] = array();
                    }
                    array_push($value->constraints[$name], $constraint);
                }
            }

            $this->buildRowAttribut($value, $form);


            /* OFFSET FORM */
            if ($form->offsetExists($key)) {
                $config = $form->offsetGet($key)->getConfig();
                $function = new \ReflectionClass($config->getType()->getInnerType());
                $type = $function->getShortName();
             
                
                /* COLLECTION */  
                if($type==="CollectionType"){
                        ///TO DO LATER 
                       /* if(count($value->children)>0){};      
                            $option = $config->getAttribute("data_collector/passed_options");
                            $formFactory = $this->container->get('form.factory');
                            $form=$formFactory->create($option["type"],new \Cms\CoreBundle\Entity\Translation); //TO DO CATCH OBJECT TYPE FORM
                            $formView=$form->createView();
                       
                            $validator = $this->container->get('validator');
                        
                            $propertyMetadataCollection = $validator->getMetadataFor($form->getData());
                            $this->getProperty($formView, $propertyMetadataCollection, $form);
                            $value->type=$formView;
                       */
              
                }
                
                /* MATCH */  
                if ($type === "RepeatedType") {
                    $option = $config->getAttribute("data_collector/passed_options");
                    $error = $option["invalid_message"];
                    foreach ($value->children as $keyChild => $valueChild) {
                        $this->buildRowAttribut($valueChild, $form);
                        $valueChild->constraints["Match"] = array();
                        $error = $translator->trans($error);
                        $valueChild->constraints["Match"][0] = array("message" => $error, "match" => $value->children["first"]->vars);
                    }
                    $value->children["first"]->constraints = $value->constraints;
                }
            }

            
              if(count($value->children)>0){
                   $this->getProperty($value,$metadata,$form);
              };
        }
    }

    function buildRowAttribut($row, $form) {
        $obj = $row->vars["name"];
        $row->vars["name"] = array();
        $row->vars["name"]["object"] = $obj;
        
        $row->vars["name"]["ngmodel"] = "data." . str_replace(array("]", "["), array("", "."), $row->vars["full_name"]);
        $pattern = "/(\.\d+\.|\.\d+$)/";
      
        /* INTEGER REPLACE */
        preg_match_all($pattern, $row->vars["name"]["ngmodel"] , $matches);
        foreach($matches[0] as $key=>$value){
            $key=str_replace(".","",$value);
            $row->vars["name"]["ngmodel"] = str_replace($value,"[".$key."].",$row->vars["name"]["ngmodel"]);
        }
     
        $row->vars["name"]["form"] = $form->getName();
    }

    function translateConstraintErrors($constraint) {
        $translator = $this->container->get('translator');
        if (property_exists($constraint, "maxMessage")) {
            $t = $translator->trans($constraint->maxMessage, array(), 'validators');
            $t = $translator->trans($t);

            $t = \preg_replace("/{{ limit }}/", "%limit%", $t);
            $t = $translator->transChoice($t, $constraint->max, array('%limit%' => $constraint->max));

            $constraint->maxMessage = $t;
        }
        if (property_exists($constraint, "minMessage")) {
            $t = $translator->trans($constraint->minMessage, array(), 'validators');
            $t = $translator->trans($t);
            $t = \preg_replace("/{{ limit }}/", "%limit%", $t);
            $t = $translator->transChoice($t, $constraint->min, array('%limit%' => $constraint->min));

            $constraint->minMessage = $t;
        }
        if (property_exists($constraint, "maxSizeMessage")) {
           
            $t = $translator->trans($constraint->maxSizeMessage, array(), 'validators');
            $t = $translator->trans($t);
              $t = \preg_replace("/\({{ size }} {{ suffix }}\)/", "", $t);
                
            $t = \preg_replace("/{{ limit }}/", $constraint->maxSize, $t);
            $t = \preg_replace("/{{ suffix }}/", "K", $t);
          
       
            $constraint->maxSizeMessage = $t;
        }
        if (property_exists($constraint, "mimeTypesMessage")) {
            $t = $translator->trans($constraint->mimeTypesMessage, array(), 'validators');
            $t = $translator->trans($t);
            $constraint->mimeTypesMessage = $t;
        }
        if (property_exists($constraint, "message")) {
            $t = $translator->trans($constraint->message, array(), 'validators');
            $t = $translator->trans($t);
            $constraint->message = $t;
        }
    }

}

