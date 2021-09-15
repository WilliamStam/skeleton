<?php

namespace Modules\System;
use Psr\Container\ContainerInterface;
use Slim\Routing\RouteCollectorProxy;
use System\Module\AbstractModule;


class Module extends AbstractModule {


    function moduleRoutes(RouteCollectorProxy $module): void {
        $module->group("", function (RouteCollectorProxy $group) {
           $group->get("",InfoController::class)->setName("system_info");

        });

    }

    function moduleContainers(ContainerInterface $container) : void {

    }


}