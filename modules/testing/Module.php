<?php

namespace Modules\testing;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\Routing\RouteCollectorProxy;
use System\Core\Profiler;
use System\Core\System;
use System\Module\AbstractModule;
use System\Slim\Generic;


class Module extends AbstractModule {


    function moduleRoutes(RouteCollectorProxy $module): void {
        $module->group("/test", function (RouteCollectorProxy $group) {
//            $group->get("",HomeController::class)->setName("testing_home");
            $group->get("[/{id}]",HomeController::class)->setName("test_home");
            $group->get("/tab/{tab}",TabController::class)->setName("home_tab");
            $group->post("/tab/{tab}",TabController::class)->setName("home_tab_post");




        });

    }

    function moduleContainers(ContainerInterface $container) : void {

    }


}