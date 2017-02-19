<?php

namespace Tools\Angular1Bundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Tools\Angular1Bundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class TestController extends Controller 
{
    /**
     *  @Route(
     *      "/angular1.html",
     *      name="symfonyFormAngular1",
     *  )
     *  @Method({"GET"})
     */
    public function symfonyFormAngular1Action(Request $request) 
    {     
        $entity = new User();
        $form = $this->container->get('form.angular');
        $tmp = $form->createFormBuilder($entity);
        $a = $tmp->add('username', TextType::class)->add('password', TextType::class)->getForm();
        
        dump($a->createView());
        $tmp = $form->createForm('Tools\Angular1Bundle\Form\UserType', $entity);
        
        dump($tmp->createView());
        exit();
        return $this->render("ToolsAngular1Bundle::form.html.twig", array());
    }
}
