<?php

namespace Tools\JsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Reference;

class ToolsJsExtension extends Extension {

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container) {
        $container
                ->register('js.form', 'Tools\JsBundle\Services\FormService');
        
        $container
                ->register('js.breeze', 'Tools\JsBundle\Services\BreezeService');
        
        $container
                ->register('js.error.builder', 'Tools\JsBundle\Services\ErrorBuilderService');
    }

}
