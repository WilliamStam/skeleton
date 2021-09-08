<?php

use Slim\App;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;
use Slim\Psr7\Response;


use \Slim\Interfaces\RouteCollectorInterface;
use Psr\Http\Message\StreamFactoryInterface;

use Zeuxisoo\Whoops\Slim\WhoopsMiddleware;



use App\Middleware\ReplaceMiddleware;
use App\Middleware\RenderMiddleware;
use App\Middleware\ProfilerMiddleware;
use App\Middleware\SystemMiddleware;
use App\Middleware\CacheControlMiddleware;
use App\Middleware\ErrorMiddleware;
use App\Middleware\SessionMiddleware;
use App\Middleware\CorsMiddleware;


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

//    $app->add(ReplaceMiddleware::class);
    $app->add(CorsMiddleware::class);


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
        // handler->addEditor( 'phpstorm', 'http://localhost:8091?message=%file:%line' );).
    } else {



        $app->add(ErrorMiddleware::class);
    }

    $app->add(new SessionMiddleware(
         $container->get(Session::class),
         $container->get(Profiler::class),
        $container->get(System::class),
    ));

//    $app->add(new ReplaceMiddleware(
//        $container->get(Replace::class),
//        $container->get(RouteCollectorInterface::class),
//        $container->get(StreamFactoryInterface::class),
//        $container->get(Profiler::class),
//        $container->get(Session::class),
//    ));


    $app->add(new ProfilerMiddleware(
        $container->get(Profiler::class),
        $container->get(System::class),
        $container->get(Replace::class),
        $container->get(StreamFactoryInterface::class),
    ));



    // Define Custom Error Handler
//    $customErrorHandler = function (
//        ServerRequestInterface $request,
//        Throwable $exception,
//        bool $displayErrorDetails,
//        bool $logErrors,
//        bool $logErrorDetails
//    ) use ($app) {
//
//        var_dump($exception->getMessage());
//        exit();
//        $payload = ['error' => $exception->getMessage()];
//
//        $response = $app->getResponseFactory()->createResponse();
//        $response->getBody()->write(
//            json_encode($payload, JSON_UNESCAPED_UNICODE)
//        );
//
//        return $response;
//    };
//
//    // Add Error Middleware
//    $errorMiddleware = $app->addErrorMiddleware(true, true, true);
//    $errorMiddleware->setDefaultErrorHandler($customErrorHandler);



//    $errorMiddleware = $app->addErrorMiddleware(true, true, true);
//    $errorMiddleware->setDefaultErrorHandler($customErrorHandler);
};