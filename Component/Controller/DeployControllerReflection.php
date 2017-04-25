<?php

namespace JsBundle\Component\Controller;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Yaml\Yaml;

final class DeployControllerReflection {
    
    private $bundle;
  
    const CONTROLLER_ALIAS = "DeployController";
       
    public function __construct(Bundle $bundle, bool $strict = false) {
        $this->bundle =$bundle;
    }
    
    public function map(){
        $extension = $bundle->getContainerExtension();
        $path = sprintf("%s\%s\%s", $this->bundle->getPath(), 'DependencyInjection', $extension->getMap());
        $map = YAML::parse(@file_get_contents($path));
        var_dump($map);
    }

}
