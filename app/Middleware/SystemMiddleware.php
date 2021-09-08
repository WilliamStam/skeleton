<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;

use System\Core\Profiler;
use System\Core\System;

class SystemMiddleware {
    public function __construct(System $System,Profiler $Profiler) {
        $this->system = $System;
        $this->profiler = $Profiler;
    }

    /**
     * System Middleware
     *
     * @param ServerRequest $request PSR-7 request
     * @param RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response {
//        var_dump("system start");
         $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);
        $GLOBALS['output'](get_class($this) . " start");
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        // return NotFound for non existent route
        if (empty($route)) {
            throw new HttpNotFoundException($request);
        }

        //    $name = $route->getName();
        //    $groups = $route->getGroups();
        //    $methods = $route->getMethods();
        //    $arguments = $route->getArguments();
        $GLOBALS['output'](get_class($this) . " setting params");
        $this->system->set('GET', (array)$request->getQueryParams());
        $this->system->set('POST', (array)$request->getParsedBody());
        $this->system->set("PARAMS", $route->getArguments());
        $this->system->set("ALIAS", $route->getName());

        $this->system->set("ROUTE", $route);
        //    $this->get(System::class)->set("ROUTE", $route);
        $profiler->stop();
        $handler = $handler->handle($request);
//        var_dump("system end");
        $GLOBALS['output'](get_class($this) . " end");
        return $handler;

    }

}