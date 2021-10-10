<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\StreamFactoryInterface;
use Slim\Psr7\Response;
use Psr\Container\ContainerInterface;

use System\Core\Profiler;
use System\Core\System;
use System\Core\Replace;

class ProfilerMiddleware {
    public function __construct(Profiler $Profiler, System $System, Replace $Replace, StreamFactoryInterface $streamFactory) {
        $this->profiler = $Profiler;
        $this->system = $System;
        $this->replace = $Replace;
        $this->streamFactory = $streamFactory;
    }

    /**
     * Profiler Middleware
     *
     * @param ServerRequest $request PSR-7 request
     * @param RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response {
//        var_dump("profiler start");
        $GLOBALS['output'](get_class($this) . " start");
        $profiler = $this->profiler->start($request->getUri(), "Route");
        $response = $handler->handle($request);
        $profiler->stop();


        if ($this->system->get("DEBUG")) {

            $body = (string)$response->getBody();
            // if its a json response
            $result = json_decode($body, true);

            $profiler_return = array(
                "url"=>$request->getUri()->getPath(),
                "method"=> $this->system->isAjax()? "XHR [{$request->getMethod()}]" : "[{$request->getMethod()}]",
                "items"=>$this->profiler->toArray(),
                "total"=>array(
                    "time"=>$this->profiler->getTotalTime(),
                    "memory"=>$this->profiler->getTotalMemory(),
                )
            );
            if (is_array($result)) {
                $result['PROFILER'] = $profiler_return;
                $body = json_encode($result, JSON_PRETTY_PRINT);
            } else {
                $body = str_replace("@@PROFILER@@", base64_encode(json_encode($profiler_return,JSON_PRETTY_PRINT)), $response->getBody());
            }
//            $newbody = new \Slim\Http\Body(fopen('php://temp', 'r+'));
//            $newbody->write($body);


            $response = $response->withBody($this->streamFactory->createStream($body));
//            $response->getBody()->write($body);

//            $this->replace->set("@@TIMER@@",$this->profiler->getTotalTime());
//            $this->replace->set("@@MEMORY@@",$this->profiler->getTotalMemory());
//
//            $response = $response->withHeader("x-profiler-time", $this->profiler->getTotalTime());
//            $response = $response->withHeader("x-profiler-memory", $this->profiler->getTotalMemory());
        }

//        $queries = \App\DB::getQueryLog();
//var_dump($queries);
//exit();


        $GLOBALS['output'](get_class($this) . " end");
//        var_dump("profiler end");
        return $response;
    }
}