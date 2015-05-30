<?php

namespace Tools\AngularBundle\Factory;

use Symfony\Component\Yaml\Yaml;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RouteCollection;
use Assetic\Factory\AssetFactory;
use Assetic\FilterManager;
use Assetic\AssetWriter;

class ModuleManager {

    private $appName;
    private $modules = array();
    private $route;

    public function __construct($appName = "app") {
        global $kernel;
        $rootDir = dirname($kernel->getRootDir());
        $this->route = new Router(new Container(), "");
        $this->twig = new \Twig_Environment(new \Twig_Loader_String());

        $this->webPath = join(DIRECTORY_SEPARATOR, array($rootDir, "/web/"));
        $this->bundlePath = join(DIRECTORY_SEPARATOR, array($rootDir, "/src/"));
        $this->appName = $appName;
        $this->appPath = join(DIRECTORY_SEPARATOR, array($this->webPath, $this->appName));
    }

    public function addModule($value) {
        $location = $this->bundlePath . $value . "\/";

        $module = new Module($location, $this->appName);
        array_push($this->modules, $module);
        return true;
    }

    public function getMenuCollection($name) {
        $menuCollection = array();

        return $menuCollection;
    }

    private function complieRoute() {

        $routeCollection = new RouteCollection();
     
        foreach ($this->modules as $key => $module) {
            $YML = $module->getRoute();
            foreach ($YML as $key => $route) {
                $routeCollection->add($key, $route);
            }
        }
        return $routeCollection;
    }

    public function matchResponse($url) {
        $routeAngular = $this->match($url);
        $controller = new ModuleController();
        return $controller->buildView($routeAngular);
    }

    public function match($url) {
        $routeCollection = $this->complieRoute();
        $matcher = new UrlMatcher($routeCollection, $this->route->getContext());
        $routeAngular = $matcher->match("/" . $this->appName . "/" . $url);
        return $routeAngular["param"];
    }

    public function compileJavascript() {
        $path = "js/";
        $fileName = $this->appName . ".modules";
        $factory = new AssetFactory($this->bundlePath);

        $fm = new FilterManager;
        $route = new Assetic\Routing($this->route);
        $fm->set('ROUTE', $route);
        $factory->setFilterManager($fm);

        if (count($this->modules) === 0) {
            throw new \Exception('You need to register at less one module');
            return;
        }

        $collection = new \Assetic\Asset\AssetCollection();
        $collection->setTargetPath($path . $fileName . ".js");

        $route = new \Assetic\Asset\StringAsset(
                $this->twig->render(file_get_contents(dirname(__FILE__) . "/Views/directive.js"), array("date" => date("Y-m-d H:i:s"), "version" => 0.1))
        );
        $collection->add($route);
        $input = array();
        array_push($input, "Tools/AngularBundle/Resources/public/js/directive/form/*.js");
        array_push($input, "Tools/AngularBundle/Resources/public/js/directive/html/*.js");
        foreach ($factory->createAsset($input) as $Asset) {
            $collection->add($Asset);
        };

        foreach ($this->modules as $name => $module) {
            $input = array();
            $filter = array("ROUTE");

            if (!array_key_exists("js", $module)) {
                throw new \Exception("Unable to find config js in module $key");
            }
            $collection->add($module->buildAsseticRoute());
            foreach ($module->getJs() as $key => $path) {
                array_push($input, $path);
            }
            foreach ($factory->createAsset($input, $filter) as $Asset) {
                $collection->add($Asset);
            }
        }
        $writer = new AssetWriter($this->webPath);

        $writer->writeAsset($collection);

        return "js/" . $fileName . ".js";
    }

}

?>