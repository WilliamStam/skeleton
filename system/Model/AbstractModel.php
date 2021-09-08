<?php

namespace System\Model;

use Psr\Container\ContainerInterface;
use System\Core\Profiler;
use System\DB\Mysql;
use System\Schema\SchemaInterface;
use System\Utilities\Arrays;


abstract class AbstractModel implements ModelInterface {



//    function __construct(ContainerInterface $container, Profiler $profiler) {

    function __construct(\App\DB $DB, Profiler $profiler) {
//        $this->DB = $container->get("DB");


        $this->DB = $DB;
        $this->profiler = $profiler;

    }

    public function __get($key) {
        return $this->DATA[$key] ?? null;
    }

    function toSchema($schema = null) {
        if (is_string($schema)) {
            $schema = new $schema();
        }

        $args_passed = func_get_args();
        array_shift($args_passed);


        if ($schema instanceof SchemaInterface){
            $args = (array)$schema->args();
            foreach ($args_passed as $k=>$v){
                $args[$k] = $v;
            }
            return call_user_func_array(array($schema->load($this), "__invoke"), $args);
        }

        if (is_callable($schema)) {
            $args = $args_passed;
            array_unshift($args,$this);


            return call_user_func_array($schema, $args);
        }


        throw new NoSchemaPassed("toSchema needs a valid schema, either a schema::class or schema object");


    }

    function rawData() {
        return $this->DATA;
    }

    function data($key, $default = null) {
        return isset($this->DATA[$key]) ? $this->DATA[$key] : $default;
    }

    function setData($key, $value = null) {
        $this->DATA[$key] = $value;
        return $this;
    }

    public function id($id = false) {

        if ($id) {
            $this->DATA[static::PK] = $id;
        }
        return $this->data(static::PK, false);
    }

    function toArray() : array {
        return (array)$this->rawData();

    }

}

class NoSchemaPassed extends \Exception {}