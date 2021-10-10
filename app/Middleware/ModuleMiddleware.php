<?php

namespace App\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;

use System\Core\Profiler;
use System\Core\System;
use System\Module\ModuleInterface;

class ModuleMiddleware {
    public function __construct(ModuleInterface $module, System $System, ContainerInterface $container, Profiler $Profiler) {
        $this->system = $System;
        $this->module = $module;
        $this->container = $container;
        $this->profiler = $Profiler;
    }

    /**
     * Module Middleware
     *
     * @param ServerRequest $request PSR-7 request
     * @param RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response {
        $GLOBALS['output'](get_class($this) . " start");
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);

        $this->system->set("MODULE", $this->module);
        $this->container->set(ModuleInterface::class,$this->module);
         $request = $request->withAttribute("MODULE",$this->module);

        $profiler->stop();
        $handler = $handler->handle($request);
        $GLOBALS['output'](get_class($this) . " end");
        return $handler;

    }

}