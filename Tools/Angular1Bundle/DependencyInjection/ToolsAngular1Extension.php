<?php

namespace Tools\Angular1Bundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Reference;

class ToolsAngular1Extension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
       $container
               ->register('form.angular', 'Tools\AngularBundle\Services\AngularService')
               ->addArgument(new Reference('form.registry'));
    }
}
