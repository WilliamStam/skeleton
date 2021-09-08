<?php

namespace Modules\Auth;
use App\Controllers\VueController;
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
        $module->group("/auth", function (RouteCollectorProxy $group) {
           $group->get("/login",Controllers\LoginController::class)->setName("login");

        });

    }

    function moduleContainers(ContainerInterface $container) : void {

    }


}