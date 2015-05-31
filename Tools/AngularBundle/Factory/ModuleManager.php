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

    public function getMenuCollection() {
        $menuCollection = array();
        foreach ($this->modules as $module) {
                       $routeCollection = $this->getRouteCollection();
                    $arrayTmp=array();
                    foreach($module->getMenu() as $menuGroupe=> $path){
                        $arrayTmp[$menuGroupe]=array();
                   
                         foreach ($path as $index=>$name){
                                     $tmpRoute= new \stdClass();
                                     $tmpRoute->name=$name;
                                      $route= $routeCollection->get($index);
                                      $defaults= $route->getDefaults();
                          
                                     $tmpRoute->path="#".$defaults["path"];
                                     array_push($arrayTmp[$menuGroupe],$tmpRoute);
                         }
                      
                    }
  
                    
                  $menuCollection=  array_merge($arrayTmp,$menuCollection);
          }
      
        return $menuCollection;
    }

    private function getRouteCollection() {

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
        $template=$controller->buildView($routeAngular);
        return $template;
    }

    public function match($url) {
        $routeCollection = $this->getRouteCollection();

        $matcher = new UrlMatcher($routeCollection, $this->route->getContext());
   
              $routeAngular = $matcher->match("/" .  $url); // TO DO TRANSFORM "/"
        return $routeAngular;
    }
    
    public function compileRoute($path) {
         $controller = new ModuleController();
    
        $routeCollection = $this->getRouteCollection()->getIterator();
        foreach($routeCollection as $route){
            
              $template=$controller->buildView($route->getDefaults());
            
              $tmp=  $this->write($this->webPath.$path.$route->getPath(), $template);
              
        }
     
       
    }
    
    public function compileJavascript() {
       
     
        
        
       $path="js/";
     
      
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
        
        $modules=array();
        foreach ($this->modules as $name => $module) {
            array_push($modules,$module->getNamespace());
            
        }
        $app = new \Assetic\Asset\StringAsset(
                $this->twig->render(file_get_contents(dirname(__FILE__) . "/Views/app.js"), array("appName" =>$this->appName, "modules" =>$modules, "date" => date("Y-m-d H:i:s")))
        );
          $collection->add($app);
        
        
        $route = new \Assetic\Asset\StringAsset(
                $this->twig->render(file_get_contents(dirname(__FILE__) . "/Views/directive.js"), array( "version" => 0.1))
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
    
     protected static function write($path, $contents)
    {
        if (!is_dir($dir = dirname($path)) && false === @mkdir($dir, 0777, true)) {
            throw new \RuntimeException('Unable to create directory '.$dir);
        }

        if (false === @file_put_contents($path, $contents)) {
            throw new \RuntimeException('Unable to write file '.$path);
        }
    }

}

?>