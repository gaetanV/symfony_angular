<?php

namespace Tools\AngularBundle\Factory;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Routing\Route;

class Module {
    /* Construct */

    private $location;
    private $outLocation;

    /* YAML */
    private $namespace;
    private $menu = array();
    private $version = 1;
    private $js = array();
    private $route_location = "route.yml";

    /* Cache */
    private $route;
    private $routeArray = array();

    public function __construct($location, $outLocation) {

        $this->outLocation = $outLocation;

        $this->location = $location;
        $this->twig = new \Twig_Environment(new \Twig_Loader_String());

        $configSrc = $this->location . "config.yml";
        if (!file_exists($configSrc)) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException("Unable to construct Angular Module $configSrc not found  ");
            return false;
        }
        $YML = Yaml::parse(file_get_contents($configSrc));

        if (array_key_exists("namespace", $YML)) {
            $this->namespace = $YML["namespace"];
        } else {
            throw new \Exception("Unable to construct Angular Module namespace not found  ");
            return false;
        }
        if (array_key_exists("menu", $YML)) {
            if (is_array($YML["menu"]))
                $this->menu = $YML["menu"];
        }

        if (array_key_exists("js", $YML)) {
            if (is_array($YML["js"])) {
                foreach ($YML["js"] as $path) {
                    array_push($this->js, $this->location . $path);
                }
            }
        }

        if (array_key_exists("route_location", $YML))
            $this->route_location = $YML["route_location"];
        if (array_key_exists("version", $YML))
            $this->version = $YML["version"];
    }

    public function getRoute() {
        if ($this->route)
            return($this->route);
        $this->route = array();
        $configSrc = $this->location . $this->route_location;
        if (!file_exists($configSrc)) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException("Unable to load Module , route : $configSrc not found");
        }
        $YML = Yaml::parse(file_get_contents($configSrc));
        foreach ($YML as $key => $route) {
            $this->routeArray[$key] = $route;
          
            if (array_key_exists("defaults", $route)) {
      
                
                if (array_key_exists("angular", $route["defaults"])) {
                    $param = $route["defaults"]["angular"];
                    
                     if (array_key_exists("path", $route)) {
                            $param["path"]=$route["path"];
                     }
                    
                    if (array_key_exists("templateUrl", $route["defaults"]["angular"])) {
                        $templateUrl = $route["defaults"]["angular"]["templateUrl"];

                        if (is_string($param["template"])) {
                            $param["template"] = $this->location . $param["template"];
                        } else
                            $param["template"]["url"] = $this->location . $param["template"]["url"];
                    }else {
                        throw new \Exception("Unable to load Route defaults angular templateUrl not found");
                        return;
                    }
                } else {
                    throw new \Exception("Unable to load Route defaults angular not found");
                    return;
                }
            } else {
                throw new \Exception("Unable to load Route defaults not found");
                return;
            }


            $this->route[$key] = new Route("/".$templateUrl, $param); // TO DO TRANSFORM "/"
            
        }

        return $this->route;
    }

    public function buildAsseticRoute() {
        $angularRoute=array();
        $routes=$this->getRoute();
        
        foreach($routes as $key=> $route){
         $angular=$route->getDefaults();
             $pattern = "/[{]([^}\/]*)(}|$|\/)/";
             $replacement = ':${1}';
              $angularRoute[$key]["path"]=preg_replace($pattern, $replacement,  $angular["path"]); 
        
              $angularRoute[$key]["templateUrl"]= $angular["templateUrl"];
              $angularRoute[$key]["controller"]=$angular["controller"];
        }

        $route = new \Assetic\Asset\StringAsset(
                $this->twig->render(file_get_contents(dirname(__FILE__) . "/Views/route.js"), array(

                    "version" => $this->version,
                    "namespace" => $this->namespace,
                    "routes" => $angularRoute,
                ))
        );
        return $route;
    }

    /* Getters */

    public function getJs() {
        return $this->js;
    }

    public function getNamespace() {
        return $this->namespace;
    }
    public function getMenu() {
           return $this->menu;
     }
}

?>