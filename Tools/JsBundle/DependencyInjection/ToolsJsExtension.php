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
                ->register('js.languages', 'Tools\JsBundle\Services\LanguagesService')
                ->addArgument('%_locale%');
        
        $container
                ->register('js.core', 'Tools\JsBundle\Services\CoreInformationService');
                
    }

}
