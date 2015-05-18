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

    public function replace_tmp_file($form) {

        if (is_array($form)) {
            foreach ($form as $key => $field) {
                if (is_array($field)) {
                    if (array_key_exists("tmp_file", $field)) {
                        $img = $form[$key]["tmp_file"]["image"];
                        $img = str_replace('data:' . $form[$key]["tmp_file"]["type"] . ';base64,', '', $img);
                        $img = str_replace(' ', '+', $img);

                        $nameTemp = sha1(uniqid(mt_rand(), true));
                        $tmpDirectory = sys_get_temp_dir() . "/" . $nameTemp;

                        if (file_put_contents($tmpDirectory, base64_decode($img))) {
                            $form[$key] = $tmpDirectory;
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
        foreach ($jsonData as $key => $form) {
            $jsonData[$key] = $this->replace_tmp_file($form);
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

            /**
             * @Constraint: init constraints and metadata 
             */
            if (!isset($value->constraints))
                $value->constraints = array();
            $metadata = $metadataForm;

            /**
             * @Constraint: new meta data ( object:entity | form )
             */
            if (is_object($value->vars["value"])) {
                $ref_object = new \ReflectionClass($value->vars["value"]);
                $objectClass = $ref_object->getShortName();
                if ($objectClass === "ArrayCollection")
                    $this->arrayCollectionToRepeat($value);
                $validator = $this->container->get('validator');
                $metadata = $validator->getMetadataFor($value->vars["value"]);

                if (count($value->children) > 0)
                    $this->getProperty($value, $metadata, $form);
                continue;
            }else {
                
            }

            /* @Define: constraints */

            if ($metadata->hasPropertyMetadata($key)) {
                $propertyMetadata = $metadata->getPropertyMetadata($key);

                foreach ($propertyMetadata[0]->constraints as $constraint) {

                    $function = new \ReflectionClass($constraint);
                    $name = $function->getShortName();
                    $this->translateConstraintErrors($constraint);
                    if (!array_key_exists($name, $value->constraints))
                        $value->constraints[$name] = array();
                    array_push($value->constraints[$name], $constraint);
                }
            }

            /* @Define: attributs */
            $this->buildRowAttribut($value, $form);



            /* OFFSET FORM */
            if ($form->offsetExists($key)) {
                $config = $form->offsetGet($key)->getConfig();
                $functionType = new \ReflectionClass($config->getType()->getInnerType());
                $type = $functionType->getShortName();

                /* COLLECTION    if($type==="CollectionType"){} /*

                  $option = $config->getAttribute("data_collector/passed_options");
                  $formFactory = $this->container->get('form.factory');
                  $form=$formFactory->create($option["type"],new \Cms\CoreBundle\Entity\Translation); //TO DO CATCH OBJECT TYPE FORM
                  $formView=$form->createView();

                  $validator = $this->container->get('validator');

                  $propertyMetadataCollection = $validator->getMetadataFor($form->getData());
                  $this->getProperty($formView, $propertyMetadataCollection, $form);
                  $value->type=$formView;
                 */
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


            if (count($value->children) > 0)
                $this->getProperty($value, $metadata, $form);
        }
    }

    /* TO DO
      private   function parseChildren($value){
      $array=Array();
      foreach ( $value->children as $child) {

      if( is_string($child->vars["value"])){
      $array[AngularService::fullNameToNgModel($child->vars["full_name"])]=$child->vars["value"];
      }
      $array=array_merge($array,$this->parseChildren($child));

      }
      return $array;
      } */

    private function arrayCollectionToRepeat($value) {
        /* TO DO
          $init="";
          $array=$this->parseChildren($value);
          foreach ($array as $key => $initValue) {
          $init=$init."$key='$initValue'";
          }

          $value->vars["attr"]["ng-init"] =$init;
         */
        if (array_key_exists("ng-repeat", $value->vars["attr"])) {
            $child = $value->children[0];

            $name = self::fullNameToNgModel($value->vars["full_name"]);
            $repeat = "item";
            $value->vars["attr"]["ng-repeat"] = "$repeat in $name";
            /**
             * @warning need a id on object
             */
            foreach ($child as $childDepth) {

                $model = str_replace($value->vars["full_name"] . "[0]", $repeat, $childDepth->vars["full_name"]);
                ;
                $childDepth->vars["attr"]["ng-model"] = str_replace(array("]", "["), array("", "."), $model);

                /* TO DO  (name ect...)
                  $pattern = "/^" . str_replace(array("]", "["), array("\]", "\["), $value->vars["full_name"]) . "\[(\d)\]/";
                  $pattern = "/^" . str_replace(array("]", "["), array("\]", "\["), $value->vars["id"]) . "_(\d)_/";
                  $childDepth->vars["id"] = preg_replace($pattern, $value->vars["id"] . "_{{" . $repeat . ".id}}_", $childDepth->vars["id"]);
                  $childDepth->vars["full_name"] = preg_replace($pattern, $value->vars["id"] . "_{{" . $repeat . ".id}}_", $childDepth->vars["full_name"]);
                 */
            }
            $value->children = array($child);
        }
    }

    private function buildRowAttribut($row, $form) {

        $formName = $form->getName();
        $row->vars["form_name"] = $formName;
        if (is_string($row->vars["value"]) && $row->vars["value"] != "") {
            $row->vars["attr"]["init-value"] = "";
        }

        if (!array_key_exists("ng-model", $row->vars["attr"])) {
            $row->vars["attr"]["ng-model"] = self::fullNameToNgModel($row->vars["full_name"]);
        }

        if (property_exists($row, "constraints")) {
            foreach ($row->constraints as $key => $constraint) {
                switch ($key) {
                    case "NotBlank":
                        break;
                    case "Regex":
                        $result = Array();
                        foreach ($constraint as $key => $endConstraint) {
                            $result["regex" . $key] = substr($endConstraint->pattern, 1, strlen($endConstraint->pattern) - 2);
                        }
                        $row->vars["attr"]["multi-pattern"] = json_encode($result);

                        break;
                    case "Length":
                        $row->vars["attr"]["ng-minlength"] = $constraint[0]->min;
                        $row->vars["attr"]["ng-maxlength"] = $constraint[0]->max;
                        break;
                    case "Image":
                        $result = Array(
                            "maxsize" => $constraint[0]->maxSize,
                            "mimeTypes" => $constraint[0]->mimeTypes
                        );
                        $row->vars["attr"]["ng-drop-file"] = json_encode($result);

                        break;
                    case "Match":
                        $row->vars["attr"]["match-field"] = $constraint[0]["match"]["attr"]["ng-model"];
                        $row->vars["attr"]["ng-disabled"] = $formName . "[" . $constraint[0]["match"]["full_name"] . "]";
                        break;
                }
            }
        }
    }

    static function fullNameToNgModel($fullname) {
        $fullname = str_replace("[]", " ", $fullname);
        $ngmodel = "data." . str_replace(array("]", "["), array("", "."), $fullname);
        $pattern = "/(\.\d+\.|\.\d+$|{{i}})/";
        preg_match_all($pattern, $ngmodel, $matches);
        foreach ($matches[0] as $key => $value) {
            $key = str_replace(".", "", $value);
            $ngmodel = str_replace($value, "[" . $key . "].", $ngmodel);
        }

        return $ngmodel;
    }

    function translateConstraintErrors($constraint) {

        $function = new \ReflectionClass($constraint);
        $type = $function->getShortName();

        $translator = $this->container->get('translator');

        switch ($type) {
            case "NotBlank":
                if (property_exists($constraint, "message")) {
                    $t = $translator->trans($constraint->message, array(), 'validators');
                    $t = $translator->trans($t);
                    $constraint->message = $t;
                }

            case "Match":
                if (property_exists($constraint, "message")) {
                    $t = $translator->trans($constraint->message, array(), 'validators');
                    $t = $translator->trans($t);
                    $constraint->message = $t;
                }

                break;
            case "Regex":
                if (property_exists($constraint, "message")) {
                    $t = $translator->trans($constraint->message, array(), 'validators');
                    $t = $translator->trans($t);
                    $constraint->message = $t;
                }

                break;
            case "Length":
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

                break;
            case "Image":

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
                break;
        }
    }

}
