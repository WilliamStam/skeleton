<?php
namespace App\Schemas;

use System\Schema\AbstractSchema;
use System\Schema\SchemaInterface;

use App\Models\TestModel;

class TestSchema extends AbstractSchema implements SchemaInterface {
    

    function __invoke($selected=null){
        /**
         * @var TestModel
         */
        $item = $this->item;

        return array(
            "id"=>$item->id,
            "name"=>$item->name,
            "email"=>$item->email,
            "active"=>$item->active,
            "selected"=>$selected

        );


        
    }
}