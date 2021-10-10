<?php

namespace App\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Log\LogLevel;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;

use System\Core\Profiler;
use System\Core\System;
use System\Core\Errors;
use System\Core\Loggers;
use App\Responders\Responder;
use System\Slim\Generic;
use System\Module\ModuleInterface;

class ErrorMiddleware {
    public function __construct(Errors $Errors, System $System, Profiler $Profiler, Responder $responder, ResponseFactory $responseFactory) {
        $this->errors = $Errors;
        $this->system = $System;
        $this->profiler = $Profiler;
        $this->responder = $responder;
        $this->responseFactory = $responseFactory;
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
        try {
            $response = $handler->handle($request);
        } catch (\Throwable $e) {
            $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);

            $level = LogLevel::ERROR;
            if (property_exists($e,"logLevel")){
               $level = $e->logLevel;
            }


            $error = $this->errors->getHandler($e)->handle($request,$e,$level);



            $payload = array(
                "code"=>$error->getCode(),
                "message"=>$error->getMessage(),
                "title"=>$error->getTitle(),
                "description"=>$error->getDescription(),
            );


            if ($this->system->get("DEBUG")){
                 $payload = array(
                    "code"=>$error->getCode(),
                    "message"=>$e->getMessage(),
                    "title"=>$error->getTitle(),
                    "description"=>$e->getFile() . ":" . $e->getLine(),
                );
            }



            $response = $this->responder->withJson($this->responseFactory->createResponse($error->getCode(),$error->getMessage()),array(
                "status"=>"error",
                "data"=>$payload,
                "messages"=>array(),
                "errors"=>array(),
            ));

            if (property_exists($e,"headers")){
                foreach ($e->headers as $header=>$value){
                    $response = $response->withHeader($header,$value);
                }
            }


            $profiler->stop();
        }

        $GLOBALS['output'](get_class($this) . " end");
        return $response;

    }

}