<?php

namespace App\Repositories;

use System\Model\AbstractModel;
use System\Model\QueryTrait;
use System\Core\Collection;
use System\Core\Profiler;

use App\Models\TestModel;

class TestRepository  {

   function __construct(\App\DB $DB, Profiler $profiler) {

        $this->profiler = $profiler;
        $this->DB = $DB;
    }

    function byId($id) : TestModel {
        $t = (new TestModel($this->DB, $this->profiler))->get($id);

        return $t;
    }
    /**
     * @return TestModel
     */
    function bySearch(string $search) : Collection {
        $t = (new TestModel($this->DB, $this->profiler))
            ->withQuery()
            ->_where("name LIKE :SEARCH OR email LIKE :SEARCH",array(
                "SEARCH"=>$search
            ))
            ->getAll();

        return $t;
    }

}
