<?php

namespace Tools\AngularBundle\Component\ConfigLoader;

use Symfony\Component\Yaml\Yaml;

final class ConfigLoaderYaml extends ConfigLoaderAbstract 
{
    /**
    * {@inheritdoc}
    */
    public function parseConfigFile(string $content):array {
        return YAML::parse($content);
    }
    
    /**
    * {@inheritdoc}
    */
    public function getExtension():string {
        return ".yml";
    }
}
