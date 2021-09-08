<?php
namespace system\db;
class Query {
    protected $DB = null;
    protected
        $WITH = null,
        $SELECT = null,
        $SELECT_APPEND = null,
        $FROM = null,
        $WHERE = null,
        $HAVING = null,
        $LIMIT = null,
        $ORDER = null,
        $ORDER_APPEND = null,
        $GROUP = null,
        $PARAMS = array()
    ;


   
    function sql() {
        $sql = array();


        if ($this->getWith()) {
            $sql[] = "WITH  {$this->getWith()['name']} AS ({$this->getWith()['def']})";
        }

        if ($this->getSelect()) {
            $sql[] = "SELECT " . $this->getSelect();
        } else {
            $sql[] = "SELECT *";
        }

        if ($this->getSelectAppend()) {
            $sql[] = "," . $this->getSelectAppend();
        }
        
        if ($this->getFrom()) {
            $sql[] = "FROM {$this->getFrom()}";
        }

        if ($this->getWhere()) {
            $sql[] = "WHERE " . $this->getWhere();
        }

        if ($this->getGroup()) {
            $sql[] = "GROUP BY " . $this->getGroup();
        }

        if ($this->getHaving()) {
            $sql[] = "HAVING " . $this->getHaving;
        }

        if ($this->getOrder()) {
            $sql[] = "ORDER BY " .$this->getOrder();

             if ($this->getOrderAppend()) {
                $sql[] = ", " .$this->getOrderAppend();
            }
        } else {
            if ($this->getOrderAppend()) {
                $sql[] = "ORDER BY " .$this->getOrderAppend();
            }
        }



        if ($this->getLimit()) {
            $sql[] = "LIMIT " . $this->getLimit();
        }

        $sql = implode(" " . PHP_EOL, (array) $sql);

        //$this->system->debug($sql);

        return $sql;

    }


    /**
     * Get the value of SELECT
     */
    public function getWith() {
        return $this->WITH;
    }

    /**
     * Set the value of SELECT
     *
     * @return  self
     */
    public function setWith(string $name,string $definition) : Query {
        $this->WITH = array(
            "name"=>$name,
            "def"=>$definition
        );

        return $this;
    }
    /**
     * Get the value of SELECT
     */
    public function getSelect() {
        return $this->SELECT;
    }

    /**
     * Set the value of SELECT
     *
     * @return  self
     */
    public function setSelect($select) : Query {
        $this->SELECT = $select;

        return $this;
    }
    /**
     * Get the value of SELECT_APPEND
     */
    public function getSelectAppend() {
        return $this->SELECT_APPEND;
    }

    /**
     * Set the value of SELECT_APPEND
     *
     * @return  self
     */
    public function setSelectAppend(string $select) : Query {
        $this->SELECT_APPEND = $select;

        return $this;
    }

    /**
     * Get the value of FROM
     */
    public function getFrom() {
        return $this->FROM;
    }

    /**
     * Set the value of FROM
     *
     * @return  self
     */
    public function setFrom($from) {
        $this->FROM = $from;

        return $this;
    }

    /**
     * Get the value of WHERE
     */
    public function getWhere() {
        return $this->WHERE;
    }

    /**
     * Set the value of WHERE
     *
     * @return  self
     */
    public function setWhere(string $where,?array $params=null) : Query {
        $this->WHERE = $where;
        if ($params){
            $this->setParams($params);
        }

        return $this;
    }

    /**
     * Get the value of HAVING
     */
    public function getHaving() {
        return $this->HAVING;
    }

    /**
     * Set the value of HAVING
     *
     * @return  self
     */
    public function setHaving($having) : Query {
        $this->HAVING = $having;

        return $this;
    }

    /**
     * Get the value of LIMIT
     */
    public function getLimit() {
        return $this->LIMIT;
    }

    /**
     * Set the value of LIMIT
     *
     * @return  self
     */
    public function setLimit($limit) : Query {
        $this->LIMIT = $limit;

        return $this;
    }

    /**
     * Get the value of ORDER
     */
    public function getOrder() {
        return $this->ORDER;
    }

    /**
     * Set the value of ORDER
     *
     * @return  self
     */
    public function setOrder($order) : Query {
        $this->ORDER = $order;

        return $this;
    }

    /**
     * Get the value of ORDER
     */
    public function getOrderAppend() {
        return $this->ORDER_APPEND;
    }

    /**
     * Set the value of ORDER
     *
     * @return  self
     */
    public function setOrderAppend($order) : Query {
        $this->ORDER_APPEND = $order;

        return $this;
    }

    /**
     * Get the value of GROUP
     */
    public function getGroup() {
        return $this->GROUP;
    }

    /**
     * Set the value of GROUP
     *
     * @return  self
     */
    public function setGroup($group) : Query {
        $this->GROUP = $group;

        return $this;
    }

    /**
     * Get the value of PARAMS
     */
    public function getParams() {
        return $this->PARAMS;
    }

    /**
     * Set the value of PARAMS
     *
     * @return  self
     */
    public function setParams($params)  : Query {
        $this->PARAMS = $params;

        return $this;
    }

    public function setFromArray(array $query = array()) : Query {

        if (array_key_exists("with",$query)){
            $this->setWith($query['with'][0],$query['with'][1]);
        }
        if (array_key_exists("select",$query)){
            $this->setSelect($query['select']);
        }

        if (array_key_exists("from",$query)){
            $this->setFrom($query['from']);
        }
        if (array_key_exists("where",$query)){
            if (is_array($query['where'])){
                 $this->setWhere($query['where'][0],$query['where'][1]);
            } else {
                $this->setWhere($query['where']);
            }

        }
        if (array_key_exists("params",$query)){
            $this->setParams($query['params']);
        }
        if (array_key_exists("order",$query)){
            $this->setOrder($query['order']);
        }
        if (array_key_exists("limit",$query)){
            $this->setLimit($query['limit']);
        }


        return $this;

    }
}