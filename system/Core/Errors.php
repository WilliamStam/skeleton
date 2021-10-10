<?php

namespace System\Core;

use System\Core\System;
use System\Errors\ErrorInterface;
use System\Exceptions\ErrorHandlerNotFound;

class Errors implements \IteratorAggregate {
    private $handlers = array();
    private $default;
    private $logger;

    function __construct() {

    }

    function addHandler(ErrorInterface $handler,$alias_codes=array()): ErrorsItem {
        return $this->handlers[] = new ErrorsItem($handler,$alias_codes);
    }

    function getIterator() {
        return new \ArrayIterator($this->handlers);
    }

    function getByCode($code): ErrorInterface {

        foreach (array_reverse($this->handlers) as $handler) {
            if (strtoupper($code) == strtoupper($handler->getCode()) || in_array($code, $handler->getAliasCodes())) {
                return $handler->getHandler();
            }
        }
        if ($this->default){
            return $this->default;
        }
        throw new ErrorHandlerNotFound("Error handler not found for code: {$code}");
    }


    function getHandler(\Throwable $exception): ErrorInterface {
        $code = $exception->getCode();

        foreach (array_reverse($this->handlers) as $handler) {

        }

        return $this->getByCode($exception->getCode());

    }



    function setDefault(ErrorInterface $handler) {
        $this->default = $handler;
        return $this;
    }


}


class ErrorsItem {
    protected $handler;
    protected $alias_codes=array();
    public function __construct(ErrorInterface $handler, $alias_codes = array()) {
        $this->handler = $handler;
        $this->alias_codes = $alias_codes;
    }
    public function __call($method, $args) {
        if (!method_exists($this->handler, $method)) {
            throw new \Exception("Undefined method $method");
        }

        return call_user_func_array(array($this->handler, $method), $args);
    }
    public function getHandler() : ErrorInterface {
        return $this->handler;
    }

    public function getAliasCodes() : array {
        return (array)$this->alias_codes;
    }

}