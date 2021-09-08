<?php

namespace System\Files;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use System\Slim\Generic;
interface FilesInterface {
    function setHandleFileExtensions();
    function getHandleFileExtensions();
    function addHandleFileExtensions();
    function response(ServerRequestInterface $request,ResponseInterface $response,string $path);




}