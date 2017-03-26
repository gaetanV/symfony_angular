<?php

namespace Cms\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller {

    private $modules = array(
        "\Cms\UserBundle\Angular\User"
    );

    public function __construct() {
        
    }

    Const TWIG_ADMIN = "CmsCoreBundle::index.html.twig";

    public function injectionAction(Request $request, $route) {

        $moduleManager = new \Tools\AngularBundle\Factory\ModuleManager( );
        foreach ($this->modules as $module) {
            $moduleManager->addModule($module);
        }
        $template = $moduleManager->matchResponse($route);

        return(new Response($template));
    }

    public function indexAction(Request $request) {

        
        if ($this->container->getParameter("kernel.environment") == "dev") {
            $moduleManager = new \Tools\AngularBundle\Factory\ModuleManager( );
            
            foreach ($this->modules as $module) {
                $moduleManager->addModule($module);
            }
            $menu = $moduleManager->getMenuCollection();

            $moduleManager->compileRoute($request->getPathInfo());
            $moduleManager->addAssetJs(array("Cms/CoreBundle/Resources/public/js/data.service.js"));

            
            return $this->render(self::TWIG_ADMIN, array(
                        "moduleMenu" => $menu["admin"],
                        "moduleJS" => $moduleManager->compileAssetJs()
            ));
        }else{
            return $this->render("ok");
        }
    }

}

/*

  $router = $this->container->get('router');
  $routeCollection = new \Symfony\Component\Routing\RouteCollection();

  foreach ($this->module as $module) {
  if (array_key_exists("route", $module)) {
  foreach ($module["route"] as $key => $routeYML) {
  $YML = Yaml::parse(file_get_contents("../src/" . $routeYML));
  foreach ($YML as $key => $r) {
  $routeCollection->add($key, new \Symfony\Component\Routing\Route($r["defaults"]["angular"]["templateUrl"], array("param" => $r["defaults"]["angular"])));
  }
  }
  }
  }

  $matcher = new \Symfony\Component\Routing\Matcher\UrlMatcher($routeCollection, $router->getContext());

  $route = $matcher->match("/app/" . $route);


  echo("+++++ For Each route :: Create The Html static ++++++++");


  return(new Response($twig->getContent())); */
/*



  $jsName = "module";
  $moduleJS = "js/" . $jsName . ".js";
  array_push($modules, \Cms\UserBundle\CmsUserBundle::$admin);
  array_push($modules, \Cms\BlogBundle\CmsBlogBundle::$admin);
  array_push($modules, \Cms\TaxonomyBundle\CmsTaxonomyBundle::$admin);
  array_push($modules, \Cms\FileBundle\CmsFileBundle::$admin);

  if ($this->container->getParameter("kernel.environment") == "dev") {

  //  "Tools/AngularBundle/Resources/public/js/*",
  $controller = "../src/";
  $factory = new AssetFactory($controller);


  $fm = new FilterManager;
  $route = new \Tools\AngularBundle\Assetic\Routing($this->container->get('router'));
  $fm->set('ROUTE', $route);

  $factory->setFilterManager($fm);

  $input = array();
  array_push($input, "Cms/CoreBundle/Resources/public/js/*.js");
  array_push($input, "Tools/AngularBundle/Resources/public/js/directive/form/*.js");
  array_push($input, "Tools/AngularBundle/Resources/public/js/directive/html/*.js");

  $filter = array("ROUTE");
  $option = array('output' => 'js/*.js', 'name' => $jsName);


  foreach ($modules as $module) {
  foreach ($module["js"] as $path) {
  // $pattern="/[:]([^:\/]*)(:|$|\/)/";
  // $path = preg_replace_callback($pattern, function ($matches) {return "{".$matches[1]."}".$matches[2];}, $moduleRoute["path"]);

  array_push($input, $path);
  }
  }

  $js = $factory->createAsset($input, $filter, $option);

  //if(!file_exists($jsPath) ||filemtime($jsPath)-$resource->getLastModified()<0){
  echo("js_module_build");
  $writer = new AssetWriter($this->container->getParameter('assetic.write_to'));
  $writer->writeAsset($js);
  // }
  } else {
  echo("prod");
  }



 */



/*
  use Assetic\Asset\FileAsset;
  use Assetic\AssetManager;
  use Assetic\Asset\AssetCollection;
  use Assetic\Asset\AssetCache;
  use Assetic\Cache\FilesystemCache;
  use Assetic\Factory\AssetFactory;
  use Assetic\Asset\GlobAsset;
  use Assetic\Factory\LazyAssetManager;
  use Symfony\Bundle\AsseticBundle\Controller\AsseticController;
  use Assetic\AssetWriter;
  use Assetic\FilterManager;
  use Assetic\Factory\Loader\FunctionCallsFormulaLoader;
  use Assetic\Factory\Resource\DirectoryResource;
  use Assetic\Factory\Resource\FileResource;
  use Symfony\Component\Yaml\Yaml;



  $resource = new AssetCollection(
  array(

  new FileAsset('../src/Tools/AngularBundle/Resources/public/js/angular.js'),
  new FileAsset('../src/Tools/AngularBundle/Resources/public/js/angular-route.js'),
  // new GlobAsset('../src/Tools/AngularBundle/Resources/public/js/form/*'),
  //   new GlobAsset('../src/Tools/AngularBundle/Resources/public/js/form/*'),
  )
  );

 * 
 *             $js=new AssetCollection(
  array(

  //    new FileAsset('../src/Tools/AngularBundle/Resources/public/js/angular.js'),
  //    new FileAsset('../src/Tools/AngularBundle/Resources/public/js/angular-route.js'),
  //      new GlobAsset($controller.'Tools/AngularBundle/Resources/public/js/directive/form/*.js'),
  //  new GlobAsset($controller.'Tools/AngularBundle/Resources/public/js/directive/html/*.js'),
  )
  );

  //   if(  filemtime("js/part1.js")<= $resource->getLastModified() ){
  echo("build");
  $resource->setTargetPath('part1.js');
  $am = new AssetManager();
  $am->set('jquery', $resource);


  $factory = new AssetFactory("/assetic");
  $am = new AssetManager();
  $factory->setAssetManager($am);


  $am = new LazyAssetManager($factory);
  $am->set('angular', $resource);



  $writer = new AssetWriter("js/");
  $writer->writeManagerAssets($am);

 */

/*
 * 
 *     $js = $factory->createAsset(array(
  '@jquery',
  'js/application.js',
  ));
  $am->setLoader('php', new FunctionCallsFormulaLoader($factory));
  $am->addResource(new DirectoryResource($js->getTargetPath()), 'php');


  \Doctrine\Common\Util\Debug::dump($am);
  $loader = new FunctionCallsFormulaLoader($factory);
  $formulae = $loader->load(new FileResource($js->getTargetPath()));

  \Doctrine\Common\Util\Debug::dump($formulae);

 */




/*
 * 
  $am = new LazyAssetManager($factory);
  $am->set('angular', $resource);

 */

//   $writer = new AssetWriter("js/");
// $writer->writeManagerAssets($am);
/*  }else{
  echo(filemtime("js/part1.js")-$resource->getLastModified());

  echo("cache");
  } */



/*
  $src=(assetic_javascripts(
  array(
  __DIR__.'/../../../@CmsCoreBundle/Resources/public/js/common/*',
  __DIR__.'/../../../@ToolsAngularBundle/Resources/public/js/common/*'
  )
  ));
 */


// $cache      = new FilesystemCache($src);
/*






  $lazyAm = new LazyAssetManager($factory);

  $resource = new AssetCollection(
  array(
  new FileAsset('../src/Tools/AngularBundle/Resources/public/js/angular.js'),
  new FileAsset('../src/Tools/AngularBundle/Resources/public/js/angular-route.js'),
  )
  );

  $resource->setTargetPath('part1.js');

  $cache      = new FilesystemCache('/css/part1');

  $lazyAm->set('jquery', $resource);

  $writer = new AssetWriter("js/");

  $writer->writeManagerAssets($lazyAm);

 * 
 * 
 *       $resource = new AssetCollection(
  array(

  new FileAsset('../src/Tools/AngularBundle/Resources/public/js/angular.js'),
  )
  );

  $resource->load();

 * 

  $cache      = new FilesystemCache('/css/44548@');
  $request    = new Request();
  $controller = new AsseticController($request, $lazyAm, $cache);
  $response   =  $controller->render('jquery');
  $response->headers->set('Content-Type', 'text/javascript');
  return $response;

  $resource = $factory->createAsset(
  array('@overall')
  );
  $response = new Response;
  $response->SetContent($resource->dump());
  $response->headers->set('Content-Type', 'text/javascript');
  return $response; */
?>