<?php


namespace System\Errors;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use System\Core\Loggers;

class Error implements ErrorInterface {

    protected $message;
    protected $code;

    protected $logger;

     protected $log_level;

    function __construct(Loggers $loggers) {
        $this->loggers = $loggers;
    }
    function load($code, $message = "") : Error {
        $this->code = $code;
        $this->message = $message;

        return $this;
    }

    function __invoke(ServerRequestInterface $request, \Throwable $exception) : ResponseInterface {



        if ($this->log_level){
            $this->loggers->getByLevel($this->log_level)->log($this->log_level,"OOPSY",array(
                "uri"=>(string)$request->getUri(),
                "file"=>$exception->getFile(),
                "line"=>$exception->getLine(),
                "message"=>$exception->getMessage(),
            ));
        }

        $response->getBody()->write(json_encode(array(
                "code"=> $this->code,
                "message"=>$this->message,
                "exception"=>$exception->getMessage() . " file:" . $exception->getFile() . ":" . $exception->getLine(),
            )));


        return $response
          ->withHeader('Content-Type', 'application/json')
          ->withStatus($this->getCode());

    }

    function getCode() : int {
        return $this->code;
    }



    function getMessage() : string {
        return $this->message;
    }

}