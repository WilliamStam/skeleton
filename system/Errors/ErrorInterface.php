<?php

namespace System\Errors;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LogLevel;
use System\Core\System;

interface ErrorInterface {

    public function getCode() : int;
    public function getMessage() : string;
    public function handle(ServerRequestInterface $request, \Throwable $exception,LogLevel $logLevel);



}