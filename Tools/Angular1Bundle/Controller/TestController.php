<?php

namespace Tools\Angular1Bundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

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
        $form = $this->container->get('form.angular');
        $tmp = $form->createFormBuilder();
        return $this->render("ToolsAngular1Bundle::form.html.twig", array());
    }
}
