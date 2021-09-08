<?php

namespace System\Exceptions;

class ErrorHandlerNotFound extends \Exception {
    protected $code = 500;
    protected $message = 'Error handler not found';
}