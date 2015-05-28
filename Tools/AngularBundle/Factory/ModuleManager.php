<?php
namespace Tools\AngularBundle\Factory;

use Symfony\Component\Yaml\Yaml;
use Assetic\Factory\AssetFactory;
use Assetic\FilterManager;
use Assetic\AssetWriter;

class ModuleManager {
    private $appName;
    private $modules = array();
    private $route;

    public function __construct(\Symfony\Bundle\FrameworkBundle\Routing\Router $route,$appName="app") {
        global $kernel;
        $rootDir=dirname($kernel->getRootDir());
        
        $this->appName=$appName;
        // $route=new \Symfony\Bundle\FrameworkBundle\Routing\Router(new  \Symfony\Component\DependencyInjection\Container(), "");
        $this->route = $route;
        $this->appPath= join(DIRECTORY_SEPARATOR, array($rootDir, "/web/$this->appName/"));
       
        $this->bundlePath= join(DIRECTORY_SEPARATOR, array($rootDir, "/src/"));
        $this->twig = new \Twig_Environment(new \Twig_Loader_String());
    }

    /**
     * 
     * @param {string} $value
     * @return {boolean}
     * @throws \Symfony\Component\Routing\Exception\ResourceNotFoundException 
     * @throws \Exception (Unable to find config repertory)
     */
    public function addBundle($value) {
        $location =  $this->bundlePath . $value . "/Angular";
        $configSrc = $location . "/config.yml";

        if (!file_exists($configSrc)) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException("Unable to construct Angular Module $configSrc not found  ");
            return false;
        }
        $YML = Yaml::parse(file_get_contents($configSrc));
        foreach ($YML as $key => $module) {


            if (!array_key_exists("repertory", $module)) {
                throw new \Exception("Unable to find config repertory in module $key");
            }
            $module["location"] = $location . $module["repertory"];
            $this->modules[$key] = $module;
        }
        return true;
    }

    private function getModuleRoute($module) {
        $configSrc = $module["location"] . "/route.yml";
        if (!file_exists($configSrc)) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException("Unable to load Module , route : $configSrc not found");
        }
        return Yaml::parse(file_get_contents($configSrc));
    }

    private function complieRoute() { 
        $routeCollection = new \Symfony\Component\Routing\RouteCollection();
        foreach ($this->modules as $key => $module) {
           
             $YML= $this->getModuleRoute($module);
              foreach ($YML as $key => $r) {
                        $routeCollection->add($key, new \Symfony\Component\Routing\Route($this->appName.$r["defaults"]["angular"]["templateUrl"], array("param" => $r["defaults"]["angular"])));
               }
        }
        return $routeCollection;
       
    }

    public function match($url) {
         $routeCollection= $this->complieRoute();
         $matcher = new \Symfony\Component\Routing\Matcher\UrlMatcher($routeCollection, $this->route->getContext());

        $route = $matcher->match("/$this->appName/" . $url);
    }

    public function compileJavascript() {

        $path = "js/";
        $fileName = "angular_module";

    
        $factory = new AssetFactory($this->bundlePath);

        $fm = new FilterManager;
        $route = new \Tools\AngularBundle\Assetic\Routing($this->route);
        $fm->set('ROUTE', $route);
        $factory->setFilterManager($fm);

        if (count($this->modules) === 0) {
            throw new \Exception('You need to register at less one module');
            return;
        }

        $collection = new \Assetic\Asset\AssetCollection();
        $collection->setTargetPath("js/angular_module.js");
        $route = new \Assetic\Asset\StringAsset(
                $this->twig->render(file_get_contents(dirname(__FILE__) . "/views/directive.js"), array("date" => date("Y-m-d H:i:s"), "version" => 0.1))
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

            $collection->add($this->buildRouteAssetic($name, $module));

            foreach ($module["js"] as $key => $javascript) {
                $path = $module["location"] . $javascript;
                array_push($input, $path);
            }
            foreach ($factory->createAsset($input, $filter) as $Asset) {
                $collection->add($Asset);
            }
        }
        $writer = new AssetWriter($this->appPath);
        
        $writer->writeAsset($collection);
        return $this->appName ."/js/". $fileName . ".js";
    }

    /**
     * 
     * @param {string} $name
     * @param {array} $module
     * @return \Assetic\Asset\StringAsset
     */
    function buildRouteAssetic($name, $module) {
        $routes = $this->getModuleRoute($module);

        $route = new \Assetic\Asset\StringAsset(
                $this->twig->render(file_get_contents(dirname(__FILE__) . "/views/route.js"), array(
                    "date" => date("Y-m-d H:i:s"),
                    "version" => 0.1,
                    "namespace" => $module["namespace"],
                    "name" => $name,
                    "routes"=>$routes,
                  
                    
                ))
        );
        return $route;
    }

}
?>