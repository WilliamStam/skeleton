<?php

namespace Modules\Admin\Permissions;

use System\Permissions\PermissionsTrait;

class SystemPermissions {
    use PermissionsTrait;

    const META = array(
        "key" => "system",
        "label" => "System",
        "description" => "System description"
    );
}