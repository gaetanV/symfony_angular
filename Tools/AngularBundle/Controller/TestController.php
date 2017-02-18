<?php

namespace Tools\AngularBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class TestController extends Controller {
    /**
     *  @Route(
     *      "/",
     *      name="symfonyFormAngular",
     *  )
     *  @Method({"GET"})
     */
    public function symfonyFormAngularAction(Request $request) {   
        return $this->render("ToolsAngularBundle::form.html.twig", array());
    }

}
