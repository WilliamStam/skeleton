<?php


namespace System\Errors;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Psr\Log\LogLevel;
use System\Core\Collection;

class Error implements ErrorInterface {

    protected $message;
    protected $code;

    protected $loggers = array();


    function __construct(Array $loggers,$code=null,$message=null) {
        $this->loggers = $loggers;
        $this->code = $code;
        $this->message = $message;
    }


    function handle(ServerRequestInterface $request, \Throwable $exception, $log_level = null): Error {

        $payload = array(
            "code" => $this->code,
            "message" => $this->message,
            "exception" => array(
                "file" => $exception->getFile(),
                "line" => $exception->getLine(),
                "message" => $exception->getMessage(),
//                "trace" => $exception->getTrace()
            ),
            "request"=>array(
                "method"=> $request->getMethod(),
                "path"=> $request->getUri()->getPath(),
                "query"=>$request->getQueryParams(),
            )
        );

        if (!$log_level){
            $log_level = LogLevel::ERROR;
        }



        foreach ($this->loggers as $logger){
            $logger->log($log_level,$exception->getMessage(),$payload);
        }


        return $this;
    }

    function getCode(): int {
        return $this->code;
    }


    function getMessage(): string {
        return $this->message;
    }

}