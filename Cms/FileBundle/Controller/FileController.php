<?php

namespace Cms\FileBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Cms\FileBundle\Entity\File;
use Cms\FileBundle\Form\FileCreate;
use Cms\FileBundle\Form\FileUpdate;

class FileController extends Controller {

    Const TWIG_FILE_ADD = "CmsFileBundle:File:add.html.twig";
    Const TWIG_FILE_UPDATE = 'CmsFileBundle:File:update.html.twig';
    Const ERROR_FILE_BD = "error.BD";

    /*     * ************* DATA ******************* */

    public function getAllAction() {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository(File::REPOSITORY)->findAll();
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
         return $response->setContent($this->get('jms_serializer')->serialize($entities,"json") );
    }

    public function getOneAction($id) {
         $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(File::REPOSITORY)->find($id);
           if (!$entity) {
            throw $this->createNotFoundException("Unable to find File entity ($id).");
        }
        
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
         return $response->setContent($this->get('jms_serializer')->serialize($entity,"json") );
    }

    /*     * ************* ACTION ******************* */

    public function addAction(Request $request) {

        $entity = new File();
        $form = $this->createForm(new FileCreate(), $entity);
        $form->handleRequest($this->get('Angular')->jsonToRequest($request));


        $mess = new \stdClass;
        $mess->valid = false;

        if ($form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                $mess->valid = true;
            } catch (\Exception $e) {
                $mess->private = $e->getMessage();
                $mess->error[0] = $this->get('translator')->trans(self::ERROR_FILE_BD);
            }
        } else {
            $mess->error = $this->get('Angular')->getErrorMessages($form);
        }
        $response = new JsonResponse($mess);
        return $response;
    }

    public function deleteAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(File::REPOSITORY)->find($id);
        $mess = new \stdClass;
        $mess->valid = false;

        if ($entity) {
            try {
                $em->remove($entity);
                $em->flush();
                $mess->valid = true;
            } catch (\Exception $e) {
                $mess->error[0] = $this->get('translator')->trans(self::ERROR_FILE_BD);
            }
        }

        $response = new JsonResponse($mess);
        return $response;
    }

    public function updateAction(Request $request, $id) {
        $mess = new \stdClass;
        $mess->valid = false;
        ;
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(File::REPOSITORY)->find($id);

        $mess->error = array();
        if ($entity) {
            $form = $this->createForm(new FileUpdate(), $entity);
            $form->handleRequest($this->get('Angular')->jsonToRequest($request));

            if ($form->isValid()) {
                $mess->valid = true;
            } else {
                $mess->error["UserUpdate"] = $this->get('Angular')->getErrorMessages($form);
            }

            if ($mess->valid) {
                try {
                    $em->flush();
                } catch (\Exception $e) {
                    $mess->error[0] = $this->get('translator')->trans(self::ERROR_FILE_BD);
                }
            }
        }

        $response = new JsonResponse($mess);
        return $response;
    }

    /**     * ************ TEMPLATE ******************* */
    public function updateTwigAction(Request $request) {
        $entity = new File();


        $form = $this->createForm(new FileUpdate(), $entity);
        //$formView = $form->createView();
        $formView = $this->get('Angular')->createFormView($form);
        return $this->render(self::TWIG_FILE_UPDATE, array(
                    'form' => $formView,
        ));
    }

    public function addTwigAction(Request $request) {
        $entity = new File();
        $form = $this->createForm(new FileCreate(), $entity);
        $formView = $this->get('Angular')->createFormView($form);
        return $this->render(self::TWIG_FILE_ADD, array(
                    'form' => $formView,
        ));
    }

}
