<?php

namespace Cms\BlogBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CmsBlogBundle extends Bundle
{
    static $admin = array(
            "module"=> array("app.article"),
            "menu" => array(
                array(
                    "title" => "article.add",
                    "path" => "#article/add",
                ), array(
                    "title" => "article.list",
                    "path" => "#article",
                )
            ),
            "js" => array(
                "Cms/BlogBundle/Resources/public/*",
                "Cms/BlogBundle/Resources/public/article/*"
            )
      
    );
}
