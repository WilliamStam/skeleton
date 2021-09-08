<?php

namespace App\Shared;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use System\Core\Profiler;
use System\Core\System;
use System\Module\AbstractModule;
use System\Slim\Generic;


class Module extends AbstractModule {


    function moduleRoutes(RouteCollectorProxy $module): void {
        $module->group("/", function (RouteCollectorProxy $group) {


        });

    }

    function moduleContainers(ContainerInterface $container) : void {
        $container->set(Repositories\TestRepository::class, function () use ($container) {
            return new Repositories\TestRepository($container->get("DB"),$container->get(Profiler::class));
        });
    }


}