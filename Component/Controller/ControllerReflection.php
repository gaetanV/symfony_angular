<?php

namespace JsBundle\Component\Controller;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Yaml\Yaml;

final class ControllerReflection {
    
    private $bundle;
    private $controllerAlias;
    
    public function __construct(Bundle $bundle, string $controllerAlias, bool $strict = false) {
        $this->bundle =$bundle;
        $this->controllerAlias = $controllerAlias;
    }
    
    public function map(){
        $extension = $bundle->getContainerExtension();
        $path = sprintf("%s\%s\%s", $this->bundle->getPath(), 'DependencyInjection', $extension->getMap());
        $map = YAML::parse(@file_get_contents($path));
        var_dump($map);
    }

}
