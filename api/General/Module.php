<?php

namespace Api\General;
use Psr\Container\ContainerInterface;
use Slim\Routing\RouteCollectorProxy;
use System\Module\AbstractModule;


class Module extends AbstractModule {


    function moduleRoutes(RouteCollectorProxy $module): void {
        $module->group("", function (RouteCollectorProxy $group) {
           $group->get("/user",Controllers\UserController::class)->setName("general_user");

        });

    }

    function moduleContainers(ContainerInterface $container) : void {

    }


}