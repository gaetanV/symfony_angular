<?php

namespace Tools\JsBundle\Component\ConfigLoader;

/**
 * ConfigLoaderInterface is the interface for get information about angular app configuration 
 */
interface ConfigLoaderInterface {

    const MASTER_CONFIG_NAME = "hook";

    /**
     * @TODO Translator : ERROR
     */
    const ERROR_FILE_NOT_FOUND   = 1;
    const ERROR_PARSE_ERROR      = 2;

    /**
    * Parse Config File
    * @param string $content
    * @return array
    */
    
    public function parseConfigFile(string $content):array ;

    /**
    * Get the Config form file extension type
    * @return string
    */
    public function getExtension(): string;
    
   
    
}
