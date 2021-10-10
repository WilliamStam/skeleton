<?php

namespace System\DB;

use System\Helpers\Collection;
use System\Core\Profiler;
use System\Utilities\Strings;

class Mysql implements DBInterface {
    protected
        $host,
        $database,
        $username,
        $password,
        $port = 3306,
        $charset = "utf8",
        $collation = "utf8_general_ci";
    protected $connection;
    protected $profiler;


    protected $table_definitions = array();

    function __construct(Profiler $Profiler) {
        $this->profiler = $Profiler;


    }

    function connect(
        $dsn = null, $username = null, $password = null, $flags = array(
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES => true,
    )
    ): Mysql {
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);
        try {
            $this->connection = new \PDO($dsn, $username, $password, $flags);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
        $profiler->stop();
        return $this;

    }


    function exec($sql, $params = array()) {
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__)
            ->setData(trim(preg_replace('/[\t\s]+/', ' ', $sql)), $params);
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);

        $records = array();
        foreach ($stmt as $record) {
            $records[] = $record;
        }
        $return = (new MysqlCollection())->load((array)$records);
        $profiler->stop();
        return $return;
    }


    function exists($table) {
        try {
            $out = $this->exec('SELECT 1 FROM ' . ($table) . ' LIMIT 1');
             if ($out instanceof MysqlCollection){
                return true;
            };
        } catch (\Throwable $e){

        }

        return false;
    }

    function beginTransaction() : void {
        $this->connection->beginTransaction();
    }
    function commit() : void {
        $this->connection->commit();
    }
    function rollback() : void {
        $this->connection->rollback();
    }


    function getTableColumns($table){
        $table = str_replace('`','',$table);

        if (!array_key_exists($table,$this->table_definitions)){
            $this->table_definitions[$table] = $this->exec("SHOW COLUMNS FROM `{$table}`");
        }

        return $this->table_definitions[$table];

    }
    function insert($table, $values = array(), $onDuplicateKeyUpdate=false) {
        $table = str_replace('`','',$table);
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);

        $params = array();
        $columns = array();

        $available_columns = array_map(function($item){ return $item['Field']; },$this->getTableColumns($table)->toArray());


        foreach ($values as $k => $v) {
            if (in_array($k,$available_columns)){
                $key = ":" . strtoupper(Strings::toAscii($k, "_"));
                $params[$key] = $v;
                $columns[] = "`{$k}`";
            }

        }

        $sql = "INSERT INTO `{$table}` (" . implode(", ", $columns) . ") VALUES (" . implode(",", array_keys($params)) . ")";

        $duplicate_columns = array();
        if ($onDuplicateKeyUpdate){
            foreach ($values as $k=>$v){
                if (in_array($k,$available_columns)) {
                    $duplicate_columns[] = "`{$k}` = VALUES(`{$k}`)";
                }
            }
            $sql = $sql . " ON DUPLICATE KEY UPDATE " . implode(",",$duplicate_columns);
        }


        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);


//        echo "<pre>";
//        var_dump([$sql, $params]);
//        echo "</pre>";

        $profiler->setData(
            trim(preg_replace('/[\t\s]+/', ' ', $sql)),
            array(
                "table" => $table,
                "values" => $values
            )
        )->stop();
        return $this->connection;
    }

    function update($table, $where, $values = array()) {
        $table = str_replace('`','',$table);
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);

        $params = array();
        $where_sql = "WHERE ";

        if (is_array($where)){
            $where_sql .= "". $where[0];
            if (isset($where[1])){
                $params = $where[1];
            }
        } else if (is_string($where)) {
            $where_sql .= "". $where;
        } else {
            throw new WhereStatementNotUnderstood("Where statement not understood");
        }




        $columns = array();

        $available_columns = array_map(function($item){ return $item['Field']; },$this->getTableColumns($table)->toArray());

        foreach ($values as $k => $v) {
            if (in_array($k,$available_columns)) {
                $key = ":" . strtoupper(Strings::toAscii($k, "_"));
                $params[$key] = $v;
                $columns[] = "`{$k}` = {$key}";
            }
        }




        $sql = "UPDATE `{$table}` SET " . implode(", ", $columns) . " $where_sql";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);

//        echo "<pre>";
//        var_dump([$sql, $params]);
//        echo "</pre>";
//        exit();

        $return = $sql;

        $profiler->setData(
            trim(preg_replace('/[\t\s]+/', ' ', $sql)),
            array(
                "table" => $table,
                "where" => $where,
                "values" => $values
            )
        )->stop();
        return $this->connection;
    }
    function delete($table, $where) {
        $table = str_replace('`','',$table);
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);

        $params = array();
        $where_sql = "";
        if (is_array($where)){
            $where_sql .= "". $where[0];
            if (isset($where[1])){
                $params = $where[1];
            }
        } else if (is_string($where)) {
            $where_sql .= "". $where;
        } else {
            throw new WhereStatementNotUnderstood("Where statement not understood");
        }

        $sql = "DELETE FROM `{$table}` WHERE $where_sql";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);

//        echo "<pre>";
//        var_dump([$sql, $params]);
//        echo "</pre>";
//        exit();


        $profiler->setData(
            trim(preg_replace('/[\t\s]+/', ' ', $sql)),
            array(
                "table" => $table,
                "where" => $where,
            )
        )->stop();
        return $this->connection;
    }


}

class MysqlCollection implements \IteratorAggregate {
    private $collection = array();

    function load($collection) {
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

    function toArray() {
        $return = array();
        foreach ($this->collection as $item) {
            $return[] = $item;
        }
        return $return;
    }




}



class WhereStatementNotUnderstood extends \Exception {
}