<?php

namespace Tools\AngularBundle\Factory;


use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RouteCollection;
use Assetic\Factory\AssetFactory;
use Assetic\FilterManager;
use Assetic\AssetWriter;
use Assetic\Asset\AssetCollection;

class ModuleManager {

    private $appName;
    private $modules = array();
    private $route;
    private $jsAssetCollection;
    
    
    public function __construct($appName = "app") {
        global $kernel;
        $rootDir = dirname($kernel->getRootDir());
       $container=$kernel->getContainer();
       

        $this->twig = new \Twig_Environment(new \Twig_Loader_String());
        $this->appName = $appName;
        
        $this->webPath = join(DIRECTORY_SEPARATOR, array($rootDir, "/web/"));
        $this->bundlePath = join(DIRECTORY_SEPARATOR, array($rootDir, "/src/"));
        $this->appPath = join(DIRECTORY_SEPARATOR, array($this->webPath, $this->appName));
        
        $this->route =$container->get("router");  
    
    }

    public function addModule($value) {
        $location = $this->bundlePath . $value . "\/";
        $module = new Module($location);
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
    
    
    private function initAssetJs() {
        $this->jsAssetCollection = new AssetCollection(); 
        $this->jsAssetCollection ->setTargetPath("js/" .  $this->appName . ".modules.js");
        
        $factory = new AssetFactory($this->bundlePath);
        $fm = new FilterManager;
        $fm->set('ROUTE',  new Assetic\Routing($this->route));
        $factory->setFilterManager($fm);

        $modules=array();
        foreach ($this->modules as $name => $module) {  array_push($modules,$module->getNamespace());   }
        
        $app = new \Assetic\Asset\StringAsset(
                $this->twig->render(file_get_contents(dirname(__FILE__) . "/Views/app.js"), array("appName" =>$this->appName, "modules" =>$modules, "date" => date("Y-m-d H:i:s")))
        );
        
        $this->jsAssetCollection->add($app);
        $route = new \Assetic\Asset\StringAsset(
                $this->twig->render(file_get_contents(dirname(__FILE__) . "/Views/directive.js"), array( "version" => 0.1))
        );
        $this->jsAssetCollection->add($route);
       
        $input = array();
        array_push($input, "Tools/AngularBundle/Resources/public/js/directive/form/*.js");
        array_push($input, "Tools/AngularBundle/Resources/public/js/directive/html/*.js");
        foreach ($factory->createAsset($input) as $Asset) {
            $this->jsAssetCollection->add($Asset);
        };
      
        foreach ($this->modules as $name => $module) {
            $input = array();
            $filter = array("ROUTE");
            $this->jsAssetCollection->add($module->buildAsseticRoute());
            foreach ($module->getJs() as $key => $path) {
                array_push($input, $path);
            }
            foreach ($factory->createAsset($input, $filter) as $Asset) {
                $this->jsAssetCollection->add($Asset);
            }
        }
    }
    
        public function addAssetJs($input=array(),$filter=array()) {
            
                if(!$this->jsAssetCollection)$this->initAssetJs();
                
                $factory = new AssetFactory($this->bundlePath);
                $fm = new FilterManager;
        
                $fm->set('ROUTE',  new Assetic\Routing($this->route));
                $factory->setFilterManager($fm);
                $filter = array("ROUTE");
                foreach ($factory->createAsset($input, $filter) as $Asset) {
                    $this->jsAssetCollection->add($Asset);
                };
        }
    
      public function compileAssetJs() {

            $writer = new AssetWriter($this->webPath);
            $writer->writeAsset($this->jsAssetCollection);
            return $this->jsAssetCollection->getTargetPath();
      }
    
      
    public function compileRoute($path) {
        $controller = new ModuleController();
        $routeCollection = $this->getRouteCollection()->getIterator();
        foreach($routeCollection as $route){
              $default=$route->getDefaults();
              $template=$controller->buildView($default);
              $tmp=  $this->write($this->webPath.$path.$default["template"], $template);
        }
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