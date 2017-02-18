<?php

namespace Tools\Angular2Bundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class TestController extends Controller 
{
    /**
     *  @Route(
     *      "/angular2.html",
     *      name="symfonyFormAngular2",
     *  )
     *  @Method({"GET"})
     */
    public function symfonyFormAngular2Action(Request $request)
    {   
        return $this->render("ToolsAngular2Bundle::form.html.twig", array());
    }
}
