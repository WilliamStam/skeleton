<?php

namespace System\Permissions;

use System\utilities\Strings;
use System\Permissions\PermissionHelper;

trait PermissionsTrait {

    function id() {
        $item = $this->getMeta();
        return $item['id'];
    }


    function getMeta() {
        $item = false;

        $item = PermissionHelper::getMeta($this);


        return $item;
    }






}