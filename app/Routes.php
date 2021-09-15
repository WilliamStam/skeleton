<?php

// Define app routes
namespace App;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Exception\HttpNotFoundException;
use System\Core\Media;
use System\Core\System;
use System\Slim\Generic;
//use System\Core\Assets;
use System\Utilities\Strings;

return function (App $app) {
    $container = $app->getContainer();

    $app->get("/", Controllers\VueController::class);




    // the media route
    $app->get("/media/{path:.*}", function ($request, $response) use ($container) {
        $system = $container->get(System::class);
        $path = Strings::fixDirSlashes($system->get("SETTINGS.media") . "/" . $system->get("PARAMS.path"));

        $response = $container->get(Media::class)->handle($request, $path);
        if ($response){
            $modified_date = gmdate("D, d M Y H:i:s",filemtime($path));
            $etag = md5($request->getUri() . "|".$modified_date);
            $response = $response->withHeader('ETag', '"'.$etag.'"');
            $response = $response->withHeader('Last-Modified', $modified_date . " GMT");
        }
        return $response;
    });
    // the assets route
    $app->get("/assets/{path:.*}", function ($request, $response) use ($container) {
        $system = $container->get(System::class);
        $path = Strings::fixDirSlashes($system->get("SETTINGS.assets") . "/assets/" . $system->get("PARAMS.path"));

        $response = $container->get(\App\Assets::class)->handle($request, $path);
        if ($response){
            $modified_date = gmdate("D, d M Y H:i:s",filemtime($path));
            $etag = md5($request->getUri() . "|".$modified_date);
            $response = $response->withHeader('ETag', '"'.$etag.'"');
            $response = $response->withHeader('Last-Modified', $modified_date . " GMT");
        }
        return $response;
    });



    $app->get("/api/@{routeName}", function (Request $request, ResponseInterface $response) use ($app) {
        $name = $request->getAttribute('routeName');
        try {
            $route = $app->getRouteCollector()->getRouteParser()->urlFor($name,$request->getQueryParams(),$request->getQueryParams());
            return $response->withHeader('Location', (string) $route);
        } catch (\Throwable $exception){
            throw new HttpNotFoundException($request,"Route named '{$name}' not found");
        }
    });

    $app->options('/api', function ($request, $response) {
        return $response;
    });
    $app->options('/api/{path:.*}', function ($request, $response) {
        return $response;
    });


    // if it doesnt match any routes then throw an error
//    $app->get("/api/{path:.*}", function (Request $request, ResponseInterface $response) {
//        throw new HttpNotFoundException($request,"Route not found");
//    });
    $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/api/{path:.*}', function ($request, $response) {
        throw new HttpNotFoundException($request);
    });



     $app->get("/{path:.*}", Controllers\VueController::class);
};

