<?php

namespace System\Core;


use System\Utilities\Arrays;
use System\Utilities\Strings;
use Twig\Environment;
use Twig\TwigFunction;
use Twig\Loader\FilesystemLoader;
use Slim\App;


class Templater {
    private $options = array();
    private $folders = array();
    private $twig;
    private $static_base = "";
    private $globals = array();

    function __construct(Profiler $Profiler = null, System $System = null, App $App = null) {
        $this->profiler = $Profiler;
        $this->system = $System;
        $this->app = $App;

        $this->twig = $this->twig($this->options);
    }
    function setFolders(array $folders=array()) : Templater {
        $this->folders = $folders;
        return $this;
    }
    function setCache($cache) : Templater {
        $this->twig->setCache($cache);
        return $this;
    }
    function setStaticBase($value) : Templater {
        $this->static_base = $value;
        return $this;
    }
    function addGlobal($key,$value){
        $this->twig->addGlobal($key,$value);
    }



    function twig($options = array()): Environment {
        $loader = new FilesystemLoader($this->folders);
        $twig = new Environment($loader, $options);


//        $twig->addGlobal("MODULES",$this->modules);


       $twig->addFunction(new TwigFunction('asset', function ($path,$module=null) {
            if (!empty($module)){
                return $module->asset($path);
            }

            return Strings::fixDirSlashes($this->static_base."/".$path,"/");
        }));







        $function = new TwigFunction('media', function ($path) {

            return "/media/" . $path;
        });
        $twig->addFunction($function);


        $function = new TwigFunction('url', function (string $routeName,array $data = [], array $queryParams = []) {
            return $this->app->getRouteCollector()->getRouteParser()->urlFor($routeName, $data, $queryParams);;
        });
        $twig->addFunction($function);






//        public function urlFor(string $routeName, array $data = [], array $queryParams = []): string
//    {
//        return $this->routeParser->urlFor($routeName, $data, $queryParams);
//    }



        return $twig;
    }


    function file($template, $data = array(), $folders = array()): string {
        $profiler = $this->profiler->start($template, "Template");
        foreach ($this->folders as $folder) {
            $folders[] = $folder;
        }
//        $data['folders'] = $folders;
//        $data['options'] = $this->options;

        $twig = $this->twig;
        $loader = new FilesystemLoader($folders);
        $twig->setLoader($loader);

        $twig->addGlobal('ROUTE', $this->system->get("ROUTE"));
        $twig->addGlobal('MODULE', $this->system->get("MODULE"));
        $twig->addGlobal('TEMPLATE', $template);




        $return = $twig->render($template, $data);
        $profiler->stop();
        return $return;
    }




}