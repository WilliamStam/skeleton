<?php

namespace Modules\Admin\Schemas;

use App\Models\CurrentUserModel;
use System\Schema\AbstractSchema;
use System\Schema\SchemaInterface;

use Modules\Admin\Models\RolesModel as Model;

class RolesListSchema extends AbstractSchema implements SchemaInterface {


    function __invoke($selected = null) {
        /**
         * @var Model
         */
        $item = $this->item;


        $return = array(
            "id" => $item->id,
            "role" => $item->role,
            "description" => $item->description,
        );
        if ($selected){
            $return['selected'] = $item->id == $selected;
        }
        if (is_array($selected)){
            $return['selected'] = in_array($item->id,$selected);
        }


        return $return;

    }
}