<?php

namespace Modules\Admin\Permissions;

use System\Permissions\PermissionsTrait;

class AdminPermissions {
    use PermissionsTrait;

    const META = array(
        "key" => "admin",
        "label" => "Admin",
        "description" => "Admin the system"
    );
//    const PARENT = SystemPermissions::class;
}