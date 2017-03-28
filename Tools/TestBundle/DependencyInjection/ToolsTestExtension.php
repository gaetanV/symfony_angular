<?php

namespace Tools\TestBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Tools\JsBundle\Command\DeployerInterface;

class ToolsTestExtension extends Extension implements DeployerInterface {

    /**
     * {@inheritdoc}
     */
    public function deployServices(): array {
        return array(
            "getUser" => [
                "route" => "/user/:id",
                "requirements" => ["id" => "\\d+"],
                "persistence" => ["User"],
                "role" => ["ROLE_USER"],
                "method" => ["GET"],
            ],
            "setUser" => [
                "route" => "/user/:id",
                "requirements" => ["id" => "\\d+"],
                "role" => ["ROLE_USER"],
                "form" => "lock_form",
                "method" => ["GET"],
            ],
        );
    }

    /**
     * {@inheritdoc}
     */
    public function deployForms(): array {
        return array(
            "lock_form" => [
                "role" => ["ROLE_USER"],
                "entityField" => [
                    "Tools/TestBundle/Entity/User" => [
                        "fields" => ["username", "password"]
                    ]
                ],
                "extraField" => [
                    "custom" => [
                        "Type" => "String",
                        "NotBlank" => "~",
                        "Length" => []
                    ]
                ]
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container) {

        $container
                ->register('js.core', 'Tools\JsBundle\Services\CoreInformationService');
    }

}
