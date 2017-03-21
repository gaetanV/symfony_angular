<?php

namespace Tools\AngularBundle\Component\ConfigLoader;

final class ConfigLoaderJson extends ConfigLoaderAbstract 
{
    
    /**
    * {@inheritdoc}
    */
    public function parseConfigFile(string $content):array {
        return (array)json_decode($content);
    }
    
    /**
    * {@inheritdoc}
    */
    public function getExtension():string {
        return ".json";
    }
}
