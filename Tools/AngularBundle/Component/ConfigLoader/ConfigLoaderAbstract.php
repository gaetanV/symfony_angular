<?php

namespace Tools\AngularBundle\Component\ConfigLoader;

use Symfony\Component\Filesystem\Exception;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * ConfigLoaderAbstract is the abstract class for get information about angular app configuration 
 *
 * @author gaetan vigneron 
 */
abstract class ConfigLoaderAbstract implements ConfigLoaderInterface {

    private $bundleAlias;
    private $bundlePath;
    private $bundleConfig;
    private $forms = array();

    /**
     * Init the Config master file
     * @param FileLocator $fileLocator
     * @param string      $bundleAlias
     * @throws \Exception When the bundle is not found
     * @throws \Exception When the file is not found
     * @throws \Exception When the file is not a valid format
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
            foreach ($this->bundleConfig["form"] as $formName => $formPath) {
                $form = @file_get_contents($this->bundlePath . $formPath);
                if (!$form) {
                    throw new \Exception(ConfigLoaderInterface::ERROR_FILE_NOT_FOUND);
                }
                try {
                    $this->forms[] = $this->parseConfigFile($form);
                } catch (\Exception $e) {
                    throw new \Exception(ConfigLoaderInterface::ERROR_PARSE_ERROR);
                }
            }
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
