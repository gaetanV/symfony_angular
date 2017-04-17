<?php

namespace JsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;


class JsExtension extends Extension {

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container) {
        $container
                ->register('js.languages', 'JsBundle\Services\LanguagesService')
                ->addArgument('%_locale%');
        
        $container
                ->register('js.core', 'JsBundle\Services\CoreInformationService');
                
    }

}
