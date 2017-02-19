<?php

namespace Tools\Angular1Bundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Tools\Angular1Bundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;

class TestController extends Controller {

    /**
     *  @Route(
     *      "/angular1.html",
     *      name="symfonyFormAngular1",
     *  )
     *  @Method({"GET"})
     */
    public function symfonyFormAngular1Action(Request $request) {
 
        $formAngular = $this->container->get('form.angular');
        //$formAngular->get("lock_form");
        //dump($form);
        exit();
        //return $this->render("ToolsAngular1Bundle::form.html.twig", array());
    }

}
