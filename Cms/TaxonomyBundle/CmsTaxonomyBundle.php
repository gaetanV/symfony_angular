<?php

namespace Cms\TaxonomyBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CmsTaxonomyBundle extends Bundle
{

    static $admin = array(
            "module"=> array(
                   "app.category"
            ),
            "menu" => array(
                array(
                    "title" => "category.list",
                    "path" => "#category",
                )
            ),
            "js" => array(
                "Cms/TaxonomyBundle/Resources/public/*",
                "Cms/TaxonomyBundle/Resources/public/category/*"
            )
      
    );
    
}
