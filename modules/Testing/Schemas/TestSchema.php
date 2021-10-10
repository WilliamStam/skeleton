<?php

namespace Modules\Testing\Schemas;

use App\Models\CurrentUserModel;
use System\Schema\AbstractSchema;
use System\Schema\SchemaInterface;

use Modules\Testing\Models\TestModel;

class TestSchema extends AbstractSchema implements SchemaInterface {


    function __invoke($selected = null) {
        /**
         * @var TestModel
         */
        $item = $this->item;

        if ($selected instanceof CurrentUserModel ){
            $selected = $selected->id();
        }

        return array(
            "id" => $item->id,
            "name" => $item->name,
            "email" => $item->email,
            "last_active" => $item->last_active,
            "selected" => $item->id == $selected

        );


    }
}