<?php

namespace System\Core;

use System\Utilities\Arrays;

class Replace  implements \IteratorAggregate {
    private $replace = array();
    private $replaced_count = 0;

    function __construct(array $replace = array()) {
        $this->replace = $replace;
    }

    function getIterator() {
        return new \ArrayIterator($this->replace);
    }
    function set($key,$value){
        $this->replace[$key] = $value;
    }


    function string(string $string) : string {

        $keys = array();
        $values = array();
        $count = 0;
        foreach ($this as $key=>$value){
            $replace_key = "/".$key."/";
            $keys[] = $replace_key;
            $values[] = $value;

            preg_match($replace_key,$string,$matches);
            $count = $count + count($matches);
        }
        $this->replaced_count = $count;
        $string = preg_replace($keys,$values,$string);
        return $string;
    }
    function count() : int {
        return $this->replaced_count;
    }
    function toArray() : array {
        return $this->replace;
    }


}