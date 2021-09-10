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
            "version"=>$item->version,
            "datetime"=>$item->datetime,
            "level"=>$item->level,
            "log"=>$item->log,
            "context"=>json_decode($item->context,true),
            "selected"=>$item->id == $selected

        );


        
    }
}