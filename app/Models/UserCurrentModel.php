<?php

namespace App\Models;

use System\Model\AbstractModel;
use System\Model\QueryTrait;
use System\Core\Collection;
use System\DB\Query;


class UserCurrentModel extends AbstractModel {
    use QueryTrait;

    protected $ID = null;
    protected $DATA = array();

    const TABLE = "system_users";
    const PK = "id";

    protected $QUERY = array(
        "select"=>"
            system_users.*
        ",
        "from"=>"
            system_users 
            LEFT JOIN system_authentication ON system_authentication.user_id = system_users.id
        ",
        "group"=>"
            system_users.id
        "
    );

    public function getByToken(string $token) : ?UserCurrentModel {
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);


        $obj = $this->withQuery();


        $obj->_where("system_authentication.token = :TOKEN", array(
            ":TOKEN" => $token
        ));

        $obj->_limit("0,1");
        $obj->DATA = $obj->DB->exec($obj->query->sql(), $obj->query->getParams())->first();

        $profiler->stop();
        return $obj;
    }
}