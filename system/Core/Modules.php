<?php

namespace System\Core;

use System\Core\System;
use System\Module\ModuleInterface;

class Modules extends Collection {


    function add($item){
        if (is_string($item)){
            try {
                $item = new $item();
            } catch (Exception $e) {
                throw new Exception("Module cant be initiated [{$item}]");
            }
        }

        if ($item instanceof ModuleInterface) {
           parent::add($item);
        } else {
            throw new Exception("Module not an instance of ModuleInterface");
        }

        return $item;
    }



    function get($module): ?ModuleInterface {
        foreach ($this->getCollection() as $item){
            if ($item->id() == $module || get_class($item) == $module){
                return $item;
            }
        }
        return null;
    }

}