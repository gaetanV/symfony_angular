<?php

namespace Tools\JsBundle\Component\ConfigLoader;

use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * ConfigLoaderAbstract is the abstract class for get information about angular app configuration 
 */

abstract class ConfigLoaderAbstract implements ConfigLoaderInterface {

    private $bundleAlias;
    private $bundlePath;
    private $bundleConfig;
    private $forms = array();

    /**
     * Init the Config master file
     * @param string $bundleAlias
     * @param FileLocator $fileLocator
     * @throws \Exception
     */
    public function __construct(string $bundleAlias, FileLocator $fileLocator) {

        $this->bundleAlias = $bundleAlias;
        $this->bundlePath = $fileLocator->locate("@" . $this->bundleAlias);

        $hook = @file_get_contents(sprintf("%s%s%s", $this->bundlePath, ConfigLoaderInterface::MASTER_CONFIG_NAME, $this->getExtension()));
        if (!$hook) {
            throw new \Exception(ConfigLoaderInterface::ERROR_FILE_NOT_FOUND);
        }
        try {
            $this->bundleConfig = $this->parseConfigFile($hook);
        } catch (\Exception $e) {
            throw new \Exception(ConfigLoaderInterface::ERROR_PARSE_ERROR);
        }
        
        if (isset($this->bundleConfig["form"])) {
           $this->forms = (array)$this->bundleConfig["form"];
        }
    }

    /**
     * Get the Master Config File
     * @return array
     */
    public function getBundleConfig():array {
        return $this->bundleConfig;
    }

    /**
     * Get the array of all Config form File
     * @return array
     */
    public function getForms():array {
        return $this->forms;
    }

}
