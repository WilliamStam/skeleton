<?php

namespace App\Models;

use System\Model\AbstractModel;
use System\Model\QueryTrait;
use System\Core\Collection;
use System\DB\Query;


class TestModel extends AbstractModel {
    use QueryTrait;

    protected $ID = null;
    protected $DATA = array();

    const TABLE = "system_logs";
    const PK = "id";

    protected $QUERY = array(
        "select"=>"
            system_logs.*
        ",
        "from"=>"
            system_logs 
        ",
        "group"=>"
            system_logs.id
        "
    );
}