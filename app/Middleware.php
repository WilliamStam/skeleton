<?php

use Slim\App;


use Zeuxisoo\Whoops\Slim\WhoopsMiddleware;
use Psr\Http\Message\StreamFactoryInterface;
use Slim\Interfaces\RouteCollectorInterface;


use App\Middleware\ReplaceMiddleware;
use App\Middleware\RenderMiddleware;
use App\Middleware\ProfilerMiddleware;
use App\Middleware\SystemMiddleware;
use App\Middleware\CacheControlMiddleware;
use App\Middleware\ErrorMiddleware;
use App\Middleware\SessionMiddleware;
use App\Middleware\CorsMiddleware;
use App\Middleware\UserMiddleware;


use System\Core\System;
use System\Core\Settings;
use System\Core\Profiler;
use System\Core\Templater;
use System\Core\Replace;
use System\Core\Errors;
use System\Core\Session;

use System\Core\Loggers;

return function (App $app) {
    $container = $app->getContainer();


    $app->add(new SystemMiddleware(
        $container->get(System::class),
        $container->get(Profiler::class),
    ));

    $app->add(new CacheControlMiddleware(
        'public',
        86400,
        true,
        $container->get(System::class)->get("DEBUG"),
        $container->get(Profiler::class),
    ));
//
    $app->add(ReplaceMiddleware::class);
    $app->add(CorsMiddleware::class);
//
    $app->add(UserMiddleware::class);


    $app->addBodyParsingMiddleware();
    $app->addRoutingMiddleware();

    $showWhoops = $container->get(System::class)->get("DEBUG") ?? false;
    if ($container->get(System::class)->isAjax()){
            $showWhoops = false;
    }



    if ((bool)$showWhoops) {
        $app->add(new WhoopsMiddleware(array(
            'enable' => true,
            'editor' => function($file, $line) {
                return "http://localhost:8091?message=%file:%line";
            },
        )));
    } else {
        $app->add(ErrorMiddleware::class);
    }

    $app->add(new SessionMiddleware(
         $container->get(Session::class),
         $container->get(Profiler::class),
        $container->get(System::class),
    ));
//
    $app->add(new ReplaceMiddleware(
        $container->get(Replace::class),
        $container->get(RouteCollectorInterface::class),
        $container->get(StreamFactoryInterface::class),
        $container->get(Profiler::class),
        $container->get(Session::class),
    ));
//
//
    $app->add(new ProfilerMiddleware(
        $container->get(Profiler::class),
        $container->get(System::class),
        $container->get(Replace::class),
        $container->get(StreamFactoryInterface::class),
    ));


};