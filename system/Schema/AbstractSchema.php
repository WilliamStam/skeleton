<?php
namespace System\Schema;

abstract class AbstractSchema {
    protected $item = null;
    protected $args = array();
    function __construct() {
        $this->args = func_get_args();
    }
    function args(){
        return $this->args;
    }
    function load($item){
        $this->item = $item;
        return $this;
    }
    function item(){
        return $this->item;
    }
}