<?php

namespace System\Model;

use System\Core\Collection;
use System\DB\Query;

trait QueryTrait {

    protected Query $query;

    function withQuery($query=null){
        $obj = clone $this;
        if ($query instanceof Query){
            $obj->query = $query;
        }

        if (empty($obj->query)){
            $obj->query = new Query();
            $obj->query->setFromArray($this->QUERY);
        }
        if (is_array($query)){
            $obj->query->setFromArray($query);
        }
        return $obj;
    }

    function get($id = null) {
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);

        $obj = $this->withQuery();

        if ($id) {
            $obj->_where(self::TABLE.".".self::PK." = :ID", array(
                ":ID" => $id
            ))
            ;
        }
        $obj->_limit("0,1");
        $obj->DATA = $obj->DB->exec($obj->query->sql(), $obj->query->getParams())->first();

        $profiler->stop();
        return $obj;
    }


    function getAll($query=null) {
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);

        $obj = $this->withQuery($query);

        $collection = new Collection();
        foreach ($obj->DB->exec($obj->query->sql(), $obj->query->getParams()) as $record) {
            $object = clone $obj;
            $object->DATA = $record;
            $collection->add($object);
        }

        $profiler->stop();
        return $collection;
    }

    function getCount($query=null) {
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);

        $obj = $this->withQuery($query);

        $sql = "WITH wrapped AS (" . $obj->query->sql() . ") SELECT count(*) as c FROM wrapped";


        $return = 0;
        foreach ($obj->DB->exec($sql, $obj->query->getParams()) as $record) {
            $return = $record['c'];
        }

        $profiler->stop();
        return $return;
    }

    

    function _with($name, $definition) {
        $this->query->setWith($name, $definition);
        return $this;
    }

    function _select($select) {
        $this->query->setSelect($select);
        return $this;
    }

    function _selectAppend($select) {
        $this->query->setSelectAppend($select);
        return $this;
    }

    function _from($from) {
        $this->query->setFrom($from);
        return $this;
    }

    function _group($group) {
        $this->query->setGroup($group);
        return $this;
    }

    function _order($order) {
        $this->query->setOrder($order);
        return $this;
    }

    function _orderAppend($order) {
        $this->query->setOrderAppend($order);
        return $this;
    }

    function _where($sql, $params = array()) {
        $this->query->setWhere($sql);
        $this->query->setParams($params);
        return $this;
    }

    function setParams($params = array()) {
        $this->query->setParams($params);
        return $this;
    }

    function _limit($limit) {
        $this->query->setLimit($limit);

        return $this;
    }
}