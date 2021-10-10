<?php


namespace System\Errors;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Psr\Log\LogLevel;
use System\Helpers\Collection;

class Error implements ErrorInterface {

    protected $includeTrace;

    protected $message;
    protected $code;
    protected $title;
    protected $description;

    protected $loggers = array();


    function __construct(Array $loggers,$message=array(
        "code"=>null,
        "message"=>null,
        "title"=>null,
        "description"=>null,
    ),$includeTrace=false) {
        $this->loggers = $loggers;
        $this->code = $message['code'] ?? null;
        $this->message = $message['message'] ?? null;
        $this->title = $message['title'] ?? null;
        $this->description = $message['description'] ?? null;

        $this->includeTrace = $includeTrace;
    }


    function handle(ServerRequestInterface $request, \Throwable $exception, $log_level = null): Error {

        if (!$this->code){
            $this->code = $exception->getCode();
        }
        if (!$this->message){
            $this->message = $exception->getMessage();
        }
        if (!$this->title){
            if (method_exists($exception,"getTitle")){
                $this->title = $exception->getTitle();
            }
        }
        if (!$this->description){
            if (method_exists($exception,"getDescription")){
                $this->description = $exception->getDescription();
            }
        }



        $payload = array(
            "code" => $this->code,
            "message" => $this->message,
            "title" => $this->title,
            "description" => $this->description,
            "exception" => array(
                "code" => $exception->getCode(),
                "file" => $exception->getFile(),
                "line" => $exception->getLine(),
                "message" => $exception->getMessage(),
            ),
            "request"=>array(
                "method"=> $request->getMethod(),
                "path"=> $request->getUri()->getPath(),
                "query"=>$request->getQueryParams(),
            )
        );

        if ($this->includeTrace){
            $payload['exception']['trace'] = array_slice($exception->getTrace(),0,3);
        }

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


    function getTitle(): ?string {
        return $this->title;
    }
    function getDescription(): ?string {
        return $this->description;
    }



}