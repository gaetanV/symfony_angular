<?php

namespace JsBundle\TestBundle\Tests\Form;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use JsBundle\Component\Form\FormReflection;

final class FormReflectionTest extends WebTestCase {

    private $container;

    const FORM_INSCRIPTION = "\JsBundle\TestBundle\Form\UserInscription";

    public function __construct() {
        $this->container = static::createClient()->getContainer();
    }

    public function testReflectionMetaType() {
        $form = new FormReflection(self::FORM_INSCRIPTION, $this->container->get('form.factory'), $this->container->get('form.registry'), $this->container->get('validator'), $this->container->get("doctrine")->getManager());
        $this->assertEquals($form->getOwnerFields()[0], "username");
    }

}
