<?php

namespace JsBundle\TestBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use JsBundle\Component\Entity\EntityReflection;
use JsBundle\Component\Entity\EntityMapping;

final class EntityMappingTest extends WebTestCase {

    private $container;

    const USER_ENTITY = "\JsBundle\TestBundle\Entity\User";

    public function __construct() {
        $this->container = static::createClient()->getContainer();
    }

    private function getEntityReflection(string $name): EntityReflection {
        return new EntityReflection($name, $this->container->get('validator'), $this->container->get("doctrine")->getManager());
    }

    public function testReflectionMetaType() {
        $entityReflection = $this->getEntityReflection(self::USER_ENTITY);
        $this->assertEquals($entityReflection->getPropertyMetaData("username")["type"], "string");
    }

    public function testEntityMappingAssert() {
        $entityReflection = new EntityMapping($this->getEntityReflection(self::USER_ENTITY), $this->container->get('translator'), $this->container->get('js.languages')->getAll(), true);
        $this->assertEquals(property_exists($entityReflection->exportAsserts("username"), 'NotBlank'), true);
    }

}
