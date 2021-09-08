<?php

namespace System\Exceptions;

class ResourceNotFound extends \Exception {
    protected $code = 404;
    protected $message = 'Resource not found';
}