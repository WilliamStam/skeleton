<?php


namespace System\Module;
use System\Utilities\Strings;
use System\Core\System;

abstract class AbstractModule implements ModuleInterface {

    protected $module_key;


    function __construct() {
        if (!$this->module_key){
            $this->module_key = $this->buildKey();
        }


    }
    function buildKey(){
        $key = get_class($this);
        $del = array('/', '\\');
        $key_parts = explode( $del[0], str_replace($del, $del[0], $key) );
        array_pop($key_parts);
        array_shift($key_parts);
        $key = Strings::toAscii(implode("-",$key_parts),"-");

        return $key;
    }
    function getKey(){
        return $this->module_key;
    }
    function id() {
        return $this->getKey();
    }

    function getPath() {
        $reflector = new \ReflectionClass(get_class($this));
        return dirname($reflector->getFileName());

    }



}