<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;

use System\Core\Profiler;
use System\Core\System;
use System\Core\Session;

class SessionMiddleware {
    public function __construct(Session $session,Profiler $Profiler,System $System) {
        $this->session = $session;
        $this->profiler = $Profiler;
        $this->system = $System;
    }

    /**
     * Session Middleware
     *
     * @param ServerRequest $request PSR-7 request
     * @param RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response {
//        var_dump("system start");
        $profiler = $this->profiler->start(__CLASS__ . "::start", __NAMESPACE__);
        $GLOBALS['output'](get_class($this) . " start");

        if (!$this->session->isStarted()) {
            $this->session->start();
        }



        //    $this->get(System::class)->set("ROUTE", $route);
        $profiler->stop();

        $request = $request->withAttribute("SESSION",$this->session);
        $handler = $handler->handle($request);

        $profiler = $this->profiler->start(__CLASS__ . "::save", __NAMESPACE__);
        $this->session->save();
        $profiler->stop();

//        var_dump("system end");
        $GLOBALS['output'](get_class($this) . " end");
        return $handler;

    }

}