<?php


use Slim\App;
use Psr\Container\ContainerInterface;
use Slim\Routing\RouteCollectorProxy;

use App\Middleware\ModuleMiddleware;

use System\Core\System;
use System\Core\Modules;
use System\Core\Assets;
use System\Core\Profiler;

use System\Utilities\Strings;

return function (App $app) {
    $container = $app->getContainer();


    $modules = $container->get(Modules::class);
    $modules->add(\Modules\testing\Module::class);
    $modules->add(\Modules\Auth\Module::class);



    // defining the module parts
    foreach ($modules as $module) {
        // assets route for each module


        if (method_exists($module, "moduleContainers")) {
            $module->moduleContainers($container);
        }
        if (method_exists($module, "moduleRoutes")) {
            $app->group("/api", function (RouteCollectorProxy $group) use ($module) {
                $module->moduleRoutes($group);
            })->add(new ModuleMiddleware($module, $container->get(System::class), $container, $container->get(Profiler::class)))
            ;
        }

    }

};

