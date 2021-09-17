<?php
namespace Modules\Auth\Schemas;

use System\Schema\AbstractSchema;
use System\Schema\SchemaInterface;

use App\Models\UserCurrentModel;

class UserSchema extends AbstractSchema implements SchemaInterface {
    

    function __invoke(){
        /**
         * @var UserCurrentModel
         */
        $item = $this->item;

        return array(
            "id"=>$item->id,
            "name"=>$item->name,
            "email"=>$item->email,
        );


        
    }
}