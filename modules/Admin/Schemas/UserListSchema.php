<?php

namespace Modules\Admin\Schemas;

use System\Schema\AbstractSchema;
use System\Schema\SchemaInterface;


class UserListSchema extends AbstractSchema implements SchemaInterface {


    function __invoke($selected = null) {

        $item = $this->item;



        return array(
            "id" => $item->id,
            "name" => $item->name,
            "email" => $item->email,
            "roles" => $item->roles,
            "active_at" => $item->active_at,
            "updated_at" => $item->updated_at,
        );


    }
}