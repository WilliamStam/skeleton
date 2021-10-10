<?php

namespace App\Middleware;

use App\Repositories\CurrentUserRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Log\LogLevel;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;

use System\Core\Profiler;
use System\Core\System;
use System\Core\Session;
use System\Core\Loggers;

class AuthMiddleware {
    public function __construct($permissions=array()) {
        $this->permissions = $permissions;
    }

    /**
     * AuthMiddleware Middleware
     *
     * @param ServerRequest $request PSR-7 request
     * @param RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response {
        $GLOBALS['output'](get_class($this) . " start");

        if (!$request->getAttribute("USER") || !$request->getAttribute("USER")->id){
            throw new HttpUnauthorizedException($request);
        }
        if (!$request->getAttribute("PERMISSIONS")->hasPermissions($this->permissions)){
             throw new HttpForbiddenException($request);
        }

        $handler = $handler->handle($request);


        $GLOBALS['output'](get_class($this) . " end");
        return $handler;

    }

}