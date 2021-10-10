<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\StreamFactoryInterface;
use Slim\Psr7\Response;

use System\Core\Profiler;
use System\Core\System;
use System\Core\Replace;
use System\Core\Templater;
use System\Core\Session;

use System\Utilities\Strings;

use Slim\Interfaces\RouteCollectorInterface;

class ReplaceMiddleware {
    public function __construct(Replace $Replace, RouteCollectorInterface $RouteCollector, StreamFactoryInterface $streamFactory, Profiler $Profiler, Session $session) {
        $this->replace = $Replace;
        $this->routes = $RouteCollector;
        $this->streamFactory = $streamFactory;
        $this->profiler = $Profiler;
        $this->session = $session;
    }

    /**
     * Replace Middleware
     *
     * @param ServerRequest $request PSR-7 request
     * @param RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response {
//        var_dump("replace start");
        $GLOBALS['output'](get_class($this) . " start");

        $response = $handler->handle($request);

//        foreach ($this->routes->getRoutes() as $item){
//            if ($item->getName()){
//                 $this->replace->set("!!".$item->getName()."!!",$item->getPattern());
//            }
//        }

        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);

        $body = (string)$response->getBody();

        $this->replace->set("@@CSRF@@", $this->session->get("CSRF"));


        $body = $this->replace->string($body);

        if ($this->replace->count()) {
            $response = $response->withBody($this->streamFactory->createStream($body));
        }

        $GLOBALS['output'](get_class($this) . " end");
//        var_dump("replace end");
        $profiler->stop();
        return $response;
    }


}