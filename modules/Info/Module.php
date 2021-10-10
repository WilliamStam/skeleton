<?php

namespace Modules\Info;
use Psr\Container\ContainerInterface;
use Slim\Routing\RouteCollectorProxy;
use System\Module\AbstractModule;


class Module extends AbstractModule {


    function moduleRoutes(RouteCollectorProxy $module): void {
        $module->group("/info", function (RouteCollectorProxy $group) {
            $group->get("/user", Controllers\UserController::class)->setName("info_user");
            $group->get("", Controllers\SystemController::class)->setName("info_system");
        });

    }

    function moduleContainers(ContainerInterface $container) : void {

    }


}