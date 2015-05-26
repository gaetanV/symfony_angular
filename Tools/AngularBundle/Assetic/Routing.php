<?php

namespace Tools\AngularBundle\Assetic;

use Assetic\Asset\AssetInterface;  
use Assetic\Filter\FilterInterface;


class Routing implements FilterInterface  
{
    public function __construct($route) {
        $this->route=$route;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $content = $asset->getContent();
   //     $pattern = "/{{\s*path\s*\(\s*['|\"]\s*(.+)\s*\s*['|\"]\)\s*}}/";
           $pattern = "/symfony[\.]path\s*\(\s*['|\"]\s*(.+)\s*\s*['|\"]\)/";
           $result = preg_replace_callback ($pattern,  function ($matches) {
            return "'".($this->route->generate($matches[1])."'"); 
        }, $content);
 
    
        $asset->setContent($result);
    }


    public function filterDump(AssetInterface $asset)
    {
    
    }


}
?>