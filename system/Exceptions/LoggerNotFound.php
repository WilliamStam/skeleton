<?php

namespace System\Exceptions;

class LoggerNotFound extends \Exception {
    protected $code = 500;
    protected $message = 'Logger not found';
}