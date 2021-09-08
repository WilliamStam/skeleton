<?php

namespace System\DB;

use System\Core\Profiler;

class Mysql implements DBInterface {
    protected
        $host,
        $database,
        $username,
        $password,
        $port=3306,
        $charset="utf8",
        $collation="utf8_general_ci"
    ;
    protected $connection;
    protected $profiler;
    function __construct(Profiler $Profiler){
        $this->profiler = $Profiler;


    }
    function connect($dsn=null,$username=null,$password=null,$flags=array(
        \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES   => true,
    )) : Mysql {
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);
        try {
             $this->connection = new \PDO($dsn, $username, $password, $flags);
        } catch (\PDOException $e) {
             throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
        $profiler->stop();
        return $this;

    }



    function exec($sql,$params=array()){
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__)->setData(trim(preg_replace('/[\t\s]+/', ' ', $sql)),$params);
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);

        $records = array();
        foreach ($stmt as $record){
            $records[] = $record;
        }
        $return = (new MysqlCollection())->load((array)$records);
        $profiler->stop();
        return $return;
    }
}

class MysqlCollection  implements \IteratorAggregate {
    private $collection = array();

    function load($collection){
        $this->collection = $collection;
        return $this;
    }


    function getIterator() {
        return new \ArrayIterator($this->collection);
    }

    function count() {
        return count($this->collection);
    }

    function first() {
        return $this->collection[0] ?? null;
    }

    function last() {
        return $this->collection[count($this->collection) - 1] ?? null;
    }

    function getCollection() {
        return $this->collection;
    }

    function toArray(){
        $return = array();
        foreach ($this->collection as $item) {
            $return[] = $item;
        }
        return $return;
    }


}