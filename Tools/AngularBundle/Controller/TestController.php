<?php
namespace Tools\AngularBundle\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Tools\AngularBundle\Entity\UserTest;

class TestController extends Controller
{
    public function setUserAction(Request $request)
    {
                  $entity = new UserTest();
                   $form = $this->createFormBuilder($entity, array('csrf_protection' => false))
                    ->add('username','text',array('label' =>'User'))
                    ->add('password', 'repeated', array(
                        'type' => 'password',
                        'invalid_message' => 'Les mots de passe doivent correspondre',
                        'first_options'  => array('label' => 'Menu.Home'),
                        'second_options' => array('label' => 'Mot de passe (validation)'),
                    ))
                    ->add('save', 'submit', array(    'attr' => array('class' => 'save'),))
                    ->getForm();
                  
                          if ($request->getMethod() == 'POST') {
                                            $form->handleRequest($request);
                                            $retour=new \stdClass();
                                            
                                            if ($form->isValid()) {
                                                $retour->error=false;
                                             }else{
                                                   $retour->error=true;
                                                   $retour->errorList=$form->getErrorsAsString();
                                             }
                                    $response = new JsonResponse($retour);
                                   return $response;
                          }
                          $formView = $this->get('Angular')->createFormView($form);
                                  return $this->render("ToolsAngularBundle:test:base.html.twig", array(
                                      'form' =>$formView 
                         ));
        }
}
