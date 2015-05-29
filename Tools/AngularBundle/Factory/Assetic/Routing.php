<?php

namespace Tools\AngularBundle\Factory\Assetic;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

class Routing implements FilterInterface {

    public function __construct(\Symfony\Bundle\FrameworkBundle\Routing\Router $route) {
        $this->route = $route;


    }

    public function filterLoad(AssetInterface $asset) {
        $content = $asset->getContent();
   

        $content = preg_replace("/symfony[\.]getBaseUrl\(\)/", "'" .$this->route->getContext()->getBaseUrl() . "'",$content);
        
        $pattern = "/symfony[\.]path\s*\(\s*['|\"]\s*(.+)\s*\s*['|\"]\)/";
        $result = preg_replace_callback($pattern, function ($matches) {
            return "'" . ($this->route->generate($matches[1]) . "'");
        }, $content);


        $asset->setContent($result);
    }

    public function filterDump(AssetInterface $asset) {
        
    }

}
     //     $pattern = "/{{\s*path\s*\(\s*['|\"]\s*(.+)\s*\s*['|\"]\)\s*}}/";
?>