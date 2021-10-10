<?php

namespace Modules\Admin\Schemas;

use App\Models\CurrentUserModel;
use System\Schema\AbstractSchema;
use System\Schema\SchemaInterface;

use Modules\Admin\Models\RolesModel as Model;

class RolesDetailsSchema extends AbstractSchema implements SchemaInterface {


    function __invoke($permissions = array()) {
        /**
         * @var Model
         */
        $item = $this->item;


        $return = array(
            "id" => $item->id,
            "role" => $item->role,
            "description" => $item->description,
        );
        $return['permissions'] = $permissions;



        return  $return;

    }
}