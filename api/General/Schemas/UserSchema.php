<?php
namespace Api\General\Schemas;

use System\Schema\AbstractSchema;
use System\Schema\SchemaInterface;

use App\Models\CurrentUserModel;

class UserSchema extends AbstractSchema implements SchemaInterface {
    

    function __invoke(){
        /**
         * @var CurrentUserModel
         */
        $item = $this->item;

        return array(
            "id"=>$item->id,
            "name"=>$item->name,
            "email"=>$item->email,
        );


        
    }
}