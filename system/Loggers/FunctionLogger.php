<?php
namespace System\Loggers;


use Psr\Log\AbstractLogger;
use System\Utilities\Strings;
use Psr\Log\LoggerInterface;

class FunctionLogger extends AbstractLogger implements LoggerInterface {

    protected $function;

    function __construct(callable $function){
        $this->function = $function;
    }

    function log($level, $message, array $context = array()){

        call_user_func($this->function,$level,$message,$context);


    }




}