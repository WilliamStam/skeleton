<?php


namespace System\Module;
use System\Utilities\Strings;
use System\Core\System;

abstract class AbstractModule implements ModuleInterface {
    protected $web_directory = "Web";
    protected $web_views = "Web\\views";
    protected $web_static = "Web\\static";
    protected $web_static_prefix = "static";
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
    function getViewPaths(){
        return array(
            Strings::fixDirSlashes($this->getPath() . "/" . $this->web_views),
            Strings::fixDirSlashes($this->getPath(). "/" . $this->web_directory)
        );
    }
    function getStaticUrl()  : string {
        return $this->web_static_prefix;
    }
    function getStaticPaths() : string {
        return Strings::fixDirSlashes($this->getPath() . "/" . $this->web_static);
    }
    function asset($asset) : string {
        return Strings::fixDirSlashes($this->getStaticUrl() . "/" . $asset,"/");
    }
    function setStaticUrl(string $value) {
        $this->web_static_prefix = $value;
        return $this;
    }


}