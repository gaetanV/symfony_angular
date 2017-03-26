<?php

namespace Cms\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Cms\UserBundle\Entity\User;
use Cms\UserBundle\Form\UserUpdate;
use Cms\UserBundle\Form\UserUpdateMdp;
use Cms\UserBundle\Form\UserCreate;

class UserController extends Controller {

    Const TWIG_USER_EMAIL_ADD = "CmsUserBundle:User:emailAdd.html.twig";
    Const TWIG_USER_ADD = "CmsUserBundle:User:add.html.twig";
    Const TWIG_USER_UPDATE = 'CmsUserBundle:User:update.html.twig';
   Const TWIG_USER_LOG = "CmsUserBundle:User:log.html.twig";
     
    Const ERROR_USER_EMAIL_REGISTER = "error.email";
    Const ERROR_USER_BD = "error.BD";
    
   
    /*     * ************* DATA ******************* */

    public function getAllAction() {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository(User::REPOSITORY)->findAll();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
         return $response->setContent($this->get('jms_serializer')->serialize($entities,"json") );
    }

    public function getOneAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(User::REPOSITORY)->find($id);
        
         if (!$entity) {
            throw $this->createNotFoundException("Unable to find User entity $id.");
        }
        
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
         return $response->setContent($this->get('jms_serializer')->serialize($entity,"json") );
    }

    
    /*     * ************* ACTION ******************* */

    public function addAction(Request $request) {

        $entity = new User();

        $form = $this->createForm(new UserCreate(), $entity, array('method' => 'POST'));

        $form->handleRequest($this->get('Angular')->jsonToRequest($request));
        $mess = new \stdClass;
        $mess->valid = false;

        if ($form->isValid()) {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($entity);
                    try {
                         $message = \Swift_Message::newInstance()
                                ->setSubject('Hello Email')
                                ->setFrom('gaetan.design@free.fr')
                                ->setTo($entity->getUsername())
                                ->setContentType("text/html")
                                ->setBody($this->renderView(self::TWIG_USER_EMAIL_ADD, array()));
                        $this->get('mailer')->send($message);
                    } catch (\Exception $e) {
                        $mess->error[0] = $this->get('translator')->trans(self::ERROR_USER_EMAIL_REGISTER);
                    }
                   
                    $mess->valid = true;
                    $em->flush();
                } catch (\Exception $e) {
                    $mess->private = $e->getMessage();
                    $mess->error[0] = $this->get('translator')->trans(self::ERROR_USER_BD);
                }
        } else {
            $mess->error = $this->get('Angular')->getErrorMessages($form);
        }
        $response = new JsonResponse($mess);
        return $response;
    }

    public function updateAction(Request $request, $id) {
        $mess = new \stdClass;
        $mess->valid = false;
        $mess->error = array();

        $jsonData = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(User::REPOSITORY)->find($id);


        if ($entity) {
            $formsIsValid = false;
            //Check law
            if (array_key_exists("UserUpdate", $jsonData)) {
                $IsformsValid = false;

                $form = $this->createForm(new UserUpdate(), $entity);
                $form->submit($jsonData["UserUpdate"]);
                if ($form->isValid()) {
                      $IsformsValid = true;
                } else {
                    $mess->error["UserUpdate"] = $this->get('Angular')->getErrorMessages($form);
                }
            }
            //Check law
            if (array_key_exists("UserUpdateMdp", $jsonData)) {
                $IsformsValid = false;

                $form = $this->createForm(new UserUpdateMdp(), $entity);
                $form->submit($jsonData["UserUpdateMdp"]);
                if ($form->isValid()) {
                    $IsformsValid = true;
                } else {
                    $mess->error["UserUpdateMdp"] = $this->get('Angular')->getErrorMessages($form);
                }
            }

            if ($IsformsValid) {
                try {
                    $em->flush();
                    $mess->valid = true;
                } catch (\Exception $e) {
                    $mess->error[0] = $this->get('translator')->trans(self::ERROR_USER_BD);
                }
            }
        }

        $response = new JsonResponse($mess);
        return $response;
    }

    public function deleteAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(User::REPOSITORY)->find($id);
        $mess = new \stdClass;
        $mess->valid = false;

        if ($entity) {
            try {
                $em->remove($entity);
                $em->flush();
                $mess->valid = true;
            } catch (\Exception $e) {
                $mess->error[0] = $e->getMessage();
            }
        }

        $response = new JsonResponse($mess);
        return $response;
    }

    /*     * ************* TEMPLATE ******************* */

    public function updateTwigAction(Request $request) {
        $entity = new User();
        $form = $this->createForm(new UserUpdate(), $entity);
        $formMdp = $this->createForm(new UserUpdateMdp(), $entity);
        $formMdpView = $this->get('Angular')->createFormView($formMdp);
        $formView = $this->get('Angular')->createFormView($form);

        return $this->render(self::TWIG_USER_UPDATE, array(
                    'form' => $formView,
                    'formMdp' => $formMdpView,
        ));
    }

    public function addTwigAction(Request $request) {
        $entity = new User();
        $form = $this->createForm(new UserCreate(), $entity);
        $formView = $this->get('Angular')->createFormView($form);
        return $this->render(self::TWIG_USER_ADD, array(
                    'form' => $formView,
        ));
    }
    
         /*     * ************* SECURITY ******************* */
   
    
  public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(self::TWIG_USER_LOG,  array(
                'last_username' => $lastUsername,
                'error'         => $error,
            )
        );
    }


    public function loginCheckAction()
    {
        // this controller will not be executed,
        // as the route is handled by the Security system
    }
    
    

}

/* $formView=$form->createView();   $task = $form->getData(); var_dump($task);  $form->bind($jsonData["form_user"]); */
?>