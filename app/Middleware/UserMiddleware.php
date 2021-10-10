<?php

namespace App\Middleware;

use App\DB;
use App\Repositories\CurrentUserRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Log\LogLevel;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;

use System\Core\Profiler;
use System\Core\System;
use System\Core\Session;
use System\Core\Loggers;

class UserMiddleware {
    public function __construct(Profiler $Profiler, System $System, Session $session, Loggers $loggers, CurrentUserRepository $userRepository) {
        $this->profiler = $Profiler;
        $this->system = $System;
        $this->session = $session;
        $this->loggers = $loggers;
        $this->userRepository = $userRepository;
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


        $token = false;
         $user= false;

        if (count($request->getHeader("Authorization"))){
            $token_header = $request->getHeader("Authorization");
            if ($token_header && count($token_header)) {

                $token_header = reset($token_header);
                if (preg_match('/^Bearer\s(.*)$/', $token_header, $match) !== false) {
                    $token = $match[1];
                }

            }
        } elseif ($this->session->get("token")){
            $token = $this->session->get("token");
        } elseif ($this->system->get("GET.token")){
            $token = $this->system->get("GET.token");
        }


//        var_dump($token);


        if ($token) {
            $user = $this->userRepository->getByToken($token);


            $request = $request->withAttribute("USER", $user);
            $request = $request->withAttribute("PERMISSIONS",$this->userRepository->permissions($user));
            $request = $request->withAttribute("TOKEN", $token);

            if ($user) {


                $this->system->set("USER", $user);

            } else {
                $this->loggers->getByName("auth")->log(LogLevel::WARNING, "Attempted auth token [{$token}]",array(
                    "token"=>$token,
                ));
            }
        }

//        $user->settings = $user->settings + 1;

//        $this->system->debug($user->id());


        // if header auth token then


        //    $this->get(System::class)->set("ROUTE", $route);
        $profiler->stop();

        $handler = $handler->handle($request);



//         $user->settings->set("test.this",$y + 1);



        if ($user){
            $this->userRepository->save($user);
        }
//        if ($user){
//            $user->save(array(
//                "last_active"=>date("Y-m-d H:i:s")
//            ));
//        }

//        var_dump("system end");
        $GLOBALS['output'](get_class($this) . " end");
        return $handler;

    }

}