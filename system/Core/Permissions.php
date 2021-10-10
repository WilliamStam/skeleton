<?php

namespace System\Core;

use System\Helpers\Collection;
use System\Permissions\PermissionInterface;
use System\Permissions\PermissionItem;

class Permissions extends Collection {


    function add($item){
        if (is_string($item)){
            try {
                $item = (new \System\Permissions\PermissionItem($item));
            } catch (Exception $e) {
                throw new Exception("Permission cant be initiated [{$item}]");
            }
        } elseif(is_array($item)){
            try {
                $item = (new \System\Permissions\PermissionItem($item));
            } catch (Exception $e) {
                throw new Exception("Permission cant be initiated [{$item}]");
            }
        }


        if ($item instanceof PermissionInterface) {
           parent::add($item);
        } else {
            throw new Exception("Permission not an instance of PermissionInterface");
        }

        return $item;
    }



    function get($permission): ?PermissionInterface {
        foreach ($this->getCollection() as $item){
            if ($item->id() == $permission || get_class($item) == $permission){
                return $item;
            }
        }
        return null;
    }

}