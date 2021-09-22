<?php

namespace Api\Auth;
use App\Controllers\VueController;
use Api\Auth\Repositories\LoginRepository;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\Routing\RouteCollectorProxy;
use System\Core\Profiler;
use System\Core\System;
use System\Module\AbstractModule;
use System\Slim\Generic;


class Module extends AbstractModule {


    function moduleRoutes(RouteCollectorProxy $routes): void {
        $routes->group("/auth", function (RouteCollectorProxy $group) {
           $group->get("/login",Controllers\LoginController::class.":get")->setName("auth_login_get");
           $group->post("/login",Controllers\LoginController::class.":post")->setName("auth_login_post");
           $group->get("/logout",Controllers\LogoutController::class)->setName("auth_logout");
        });

    }

    function moduleContainers(ContainerInterface $container) : void {

//        $container->set(LoginRepository::class)
    }


}