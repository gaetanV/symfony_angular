<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel {

    public function registerBundles() {
        return [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new JsBundle\JsBundle(),
            new JsBundle\TestBundle\TestBundle(),
        ];
    }

    public function getCacheDir() {
        return sys_get_temp_dir() . '/var/cache/test';
    }

    public function getLogDir() {
        return sys_get_temp_dir() . '/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader) {
        $loader->load($this->getRootDir() . '/config_test.yml');
    }

}