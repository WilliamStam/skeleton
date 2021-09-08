<?php
namespace System\Files;

use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Stream;
use System\Exceptions\ResourceNotFound;
use System\Slim\Response;
use System\Utilities\Strings;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AbstractFiles implements FilesInterface {
    protected $file_extensions = array();

    protected $headers = array();

    protected $body;



    function setHandleFileExtensions(array $file_extensions=array()) {
        $this->file_extensions = $file_extensions;
        return $this;
    }
    function addHandleFileExtensions(string $file_extension="") {
        if ($file_extension){
            $this->file_extensions[] = strtolower($file_extension);
        }

        return $this;
    }
    function getHandleFileExtensions() {
        return array_keys($this->file_extensions);
    }
    function getHeaders() : array {
        return $this->headers;
    }
    function getContentType(string $path) : string {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));


        return $this->file_extensions[$extension] ?? "text/text";
    }
    function response(ServerRequestInterface $request,ResponseInterface $response,$path) : ResponseInterface {
        if (!file_exists($path)){
            throw new ResourceNotFound($request->getUri(),404);
        }
        return $this->handle($request,$response,$path);
    }
    function handle(ServerRequestInterface $request,ResponseInterface $response,$path) : ResponseInterface  {
        $response = $response->withHeader("Content-type",$this->getContentType($path));
        foreach ($this->getHeaders() as $header=>$value){
            $response = $response->withHeader($header,$value);
        }
        return $response->withBody((new StreamFactory())->createStreamFromFile($path,'r'));
    }


}