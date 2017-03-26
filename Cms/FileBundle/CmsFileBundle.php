<?php

namespace Cms\FileBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CmsFileBundle extends Bundle
{
        static $admin = array(
            "module"=> array("app.file","app.gallery"),
            "menu" => array(
                array(
                    "title" => "file.list",
                    "path" => "#file",
                ), array(
                    "title" => "gallery.list",
                    "path" => "#gallery",
                )
            ),
            "js" => array(
                "Cms/FileBundle/Resources/public/*",
                "Cms/FileBundle/Resources/public/gallery/*",
                "Cms/FileBundle/Resources/public/file/*"
            )
      
    );

   
}
