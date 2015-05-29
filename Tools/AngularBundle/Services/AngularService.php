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
        $formView =new \Tools\AngularBundle\Form\AngularForm($form);
        return $formView->createView();
    }

}
