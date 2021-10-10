<?php
namespace App\Schemas;

use System\Schema\AbstractSchema;
use System\Schema\SchemaInterface;

use App\Models\CurrentUserModel;

class CurrentUserSchema extends AbstractSchema implements SchemaInterface {
    

    function __invoke($permissions=array()){

        $item = $this->item;
        $return = array(
            "id"=>$item->id,
            "name"=>$item->name,
            "email"=>$item->email,
            "permissions"=>$permissions
        );






        return $return;


        
    }
}