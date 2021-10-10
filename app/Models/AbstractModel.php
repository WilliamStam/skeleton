<?php

namespace App\Models;

use System\Exceptions\Schemas\NoSchemaPassed;
use System\Schema\SchemaInterface;

abstract class AbstractModel extends \Illuminate\Database\Eloquent\Model {
    function toSchema($schema = null) {

        if (is_string($schema)) {
            $schema = new $schema();
        }

        $args_passed = func_get_args();
        array_shift($args_passed);

        if ($schema instanceof SchemaInterface) {
            $args = (array)$schema->args();
            foreach ($args_passed as $k => $v) {
                $args[$k] = $v;
            }
            return call_user_func_array(array($schema->load($this), "__invoke"), $args);
        }
        if (is_callable($schema)) {
            $args = $args_passed;
            array_unshift($args, $this);


            return call_user_func_array($schema, $args);
        }
        throw new NoSchemaPassed("toSchema needs a valid schema, either a schema::class or schema object");
    }
}