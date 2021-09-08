<?php

namespace System\Core;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use System\Files\FilesInterface;

use Slim\Psr7\Stream;

use System\Exceptions\ResourceNotFound;

class Media {
    /**
     * @var $handlers FilesInterface[]
     */
    protected $handlers = array();
    protected $etag_prefix;

    function __construct(ResponseInterface $response) {
        $this->response = $response;
    }


    function addHandler(FilesInterface $handler) : Media {
        $this->handlers[] = $handler;

        return $this;
    }

    function handle(ServerRequestInterface $request,string $path): ResponseInterface {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        foreach ($this->handlers as $handler){
            if (in_array($ext,$handler->getHandleFileExtensions())){
                return $handler->response($request,$this->response,$path);
            }
        }





        throw new ResourceNotFound("File not found: " . $request->getUri());

    }




}