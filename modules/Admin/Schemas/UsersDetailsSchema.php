<?php

namespace Modules\Admin\Schemas;

use App\Models\CurrentUserModel;
use System\Schema\AbstractSchema;
use System\Schema\SchemaInterface;


class UsersDetailsSchema extends AbstractSchema implements SchemaInterface {


    function __invoke() {

        $item = $this->item;


        $return = array(
            "id" => $item->id,
            "name" => $item->name,
            "email" => $item->email,
            "roles" => $item->roles,
        );


        return  $return;

    }
}