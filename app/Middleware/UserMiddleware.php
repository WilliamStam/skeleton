<?php

namespace App\Middleware;

use App\Models\CurrentUserModel;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;

use System\Core\Profiler;
use System\Core\System;
use System\Core\Session;

class UserMiddleware {
    public function __construct(Profiler $Profiler,System $System, CurrentUserModel $currentUser) {
        $this->profiler = $Profiler;
        $this->system = $System;
        $this->currentUser = $currentUser;
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


//        setId
        $token = $request->getHeader("Authorization");

        if ($token && count($token)){
            $token = reset($token);
            if ($token){
               if (preg_match('/^Bearer\s(.*)$/', $token, $match) !== false) {
                    $user = $this->currentUser->getByToken($match[1]);
                    if ($user){
                        $request = $request->withAttribute("TOKEN",$match[1]);
                        $request = $request->withAttribute("USER",$user);
                        $this->system->set("USER",$user);
                    }
                }
            }
        }




//        $this->system->debug($user->id());





        // if header auth token then


        //    $this->get(System::class)->set("ROUTE", $route);
        $profiler->stop();



        $handler = $handler->handle($request);

//        var_dump("system end");
        $GLOBALS['output'](get_class($this) . " end");
        return $handler;

    }

}