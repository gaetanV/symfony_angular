<?php

namespace Tools\AngularBundle\Factory;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Routing\Route;

class Module {
    /* Construct */

    public $location;
    public $outLocation;

    /* YAML */
    public $namespace;
    public $version = 1;
    public $js = array();
    public $route_location = "route.yml";

    /* Cache */
    public $route;
    public $routeArray= array();
 
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
             $this->routeArray[$key]=$route;
             
            if (array_key_exists("defaults", $route)) {
                if (array_key_exists("angular", $route["defaults"])) {
                    $param = $route["defaults"]["angular"];
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

       
         $this->route[$key]= new Route($this->outLocation . $templateUrl, array("param" => $param));
         
                
    
        }

        return $this->route;
    }

    public function getJs() {
        return $this->js;
    }

    public function buildAsseticRoute() {
        $this->getRoute();
        $routes = $this->routeArray;
   
        $pattern = "/[{]([^}\/]*)(}|$|\/)/";
        $replacement = ':${1}';

        foreach ($routes as $key => $route) {
            $routes[$key]["path"] = preg_replace($pattern, $replacement, $route["path"]);
        }

        $route = new \Assetic\Asset\StringAsset(
                $this->twig->render(file_get_contents(dirname(__FILE__) . "/Views/route.js"), array(
                    "date" => date("Y-m-d H:i:s"),
                    "version" => $this->version,
                    "namespace" => $this->namespace,
                    "routes" => $routes,
                ))
        );
        return $route;
    }

}

?>