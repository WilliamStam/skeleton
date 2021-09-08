<?php

namespace System\Core;

use Psr\Log\LoggerInterface;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use System\Exceptions\LoggerNotFound;
use System\Utilities\Arrays;
use System\Utilities\Strings;


class Loggers implements \IteratorAggregate {
    /**
     * @var $handlers Logger[]
     */
    protected $handlers = array();

    function __construct(System $System) {
        $this->system = $System;
    }

    function addHandler(LoggerInterface $handler, $name = '', array $log_levels = array()): LoggersItem {
        $name = $name ?: get_class($handler);
        return $this->handlers[] = new LoggersItem($handler, $name, $log_levels);
    }

    function getIterator() {
        return new \ArrayIterator($this->handlers);
    }

    function getByName($name): LoggerInterface {
        foreach (array_reverse($this->handlers) as $handler) {
            if ($handler->getName() == $name || get_class($handler->getHandler()) == $name) {
                return $handler->getHandler();
            }
        }

        throw new LoggerNotFound("Logger not found: " . $name);
    }

    function getByLevel($level) {
        $collection = new Collection();
        foreach ($this->handlers as $handler) {
            if (in_array($level, $handler->getLogLevels())) {
                $collection->add($handler->getHandler());
            }
        }

        return $collection;
    }


}

class LoggersItem {
    protected $handler;
    protected $name;
    protected $log_levels = array();

    public function __construct(LoggerInterface $handler, $name = '', array $log_levels = array()) {
        $this->handler = $handler;
        $this->name = $name;
        $this->log_levels = $log_levels;
    }

    public function __call($method, $args) {
        if (!method_exists($this->handler, $method)) {
            throw new \Exception("Undefined method $method");
        }

        return call_user_func_array(array($this->handler, $method), $args);
    }

    public function getHandler(): LoggerInterface {
        return $this->handler;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getLogLevels(): array {
        return (array)$this->log_levels;
    }

}