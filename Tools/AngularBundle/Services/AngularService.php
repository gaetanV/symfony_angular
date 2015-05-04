<?php

namespace Tools\AngularBundle\Services;

use \Symfony\Component\DependencyInjection\ContainerAware;
use \Symfony\Component\Translation\Loader\XliffFileLoader;

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

    public function jsonToRequest($request) {
        ///TRANFORM IMAGE TO FILE

        $jsonData = json_decode($request->getContent(), true);
        $request->request->replace($jsonData);
        return $request;
    }

    public function createFormView($form) {
        $formView = $form->createView();
        $this->container->get('twig')->getExtension('form')->renderer->setTheme($formView, "ToolsAngularBundle::fields.html.twig");
        $validator = $this->container->get('validator');
        // $validator = new \Symfony\Component\Validator\Mapping\ClassMetadataFactory();
        $propertyMetadata = $validator->getMetadataFor($form->getData());
        $this->getProperty($formView, $propertyMetadata, $form);
        return $formView;
    }

    private function getProperty($formView, $metadata, $form) {
        $translator = $this->container->get('translator');

        foreach ($formView->children as $key => $value) {
            if (!isset($value->constraints))
                $value->constraints = array();
            /* METADATA PROPERTY */
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
                /* MATCH */
                if ($type === "RepeatedType") {
                    $option = $config->getAttribute("data_collector/passed_options");
                    $error = $option["invalid_message"];
                    foreach ($value->children as $keyChild => $valueChild) {
                        $this->buildRowAttribut($valueChild, $form);

                        /*   $obj=$value->vars["name"];
                          $valueChild->vars["name"]=array();
                          $valueChild->vars["name"]["object"]=$obj;
                          $valueChild->vars["name"]["ngmodel"]="data.".str_replace( array("]", "["),array("","."), $valueChild->vars["full_name"]);
                          $valueChild->vars["name"]["form"]=$form->getName(); */

                        $valueChild->constraints["Match"] = array();
                        $error = $translator->trans($error);
                        $valueChild->constraints["Match"][0] = array("message" => $error, "match" => $value->children["first"]->vars);
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

    function buildRowAttribut($row, $form) {
        $obj = $row->vars["name"];
        $row->vars["name"] = array();
        $row->vars["name"]["object"] = $obj;
        $row->vars["name"]["ngmodel"] = "data." . str_replace(array("]", "["), array("", "."), $row->vars["full_name"]);
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
            $t = \preg_replace("/{{ limit }}/", $constraint->maxSize / 1000000, $t);
            $t = \preg_replace("/{{ suffix }}/", "Mo", $t);
            $constraint->maxSizeMessage = $t;
        }
        if (property_exists($constraint, "mimeTypesMessage")) {
            $t = $translator->trans($constraint->mimeTypesMessage, array(), 'validators');
            $t = $translator->trans($t);
            $constraint->mimeTypesMessage = $t;
        }
        if (property_exists($constraint, "message")) {
            //TO DO TRANSFERT DOMAIN
            $t = $translator->trans($constraint->message, array(), 'validators');
            $t = $translator->trans($t);
            $constraint->message = $t;
        }
    }

}
