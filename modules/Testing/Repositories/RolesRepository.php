<?php


namespace Modules\Testing\Repositories;

use App\DB2;
use Modules\Testing\Models\RolesModel;

use System\Core\Profiler;
use System\Core\System;

use System\Helpers\Pagination;
use System\Helpers\Collection;


class RolesRepository {
    protected $model;

    function __construct(System $System, Profiler $profiler) {
        $this->system = $System;
        $this->profiler = $profiler;

        $this->model = RolesModel::query();
    }


    function getList(
        ?string $search = null,
        int $page = 1,
        string $order = "role",
        string $direction="asc"
    ) {
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);


        $search = "";
        $records = $this->model;

        if ($search){
            $search = '%'.$search.'%';
            $records->where("role",'LIKE',$search);
            $records->orWhere("description",'LIKE',$search);
        }

//        var_dump($records->toSql());
        $recordCount = $records->count();
//        var_dump($recordCount);

        $pagination = new Pagination(2, 10);

        $page = 1;
        $pagination->calculate($recordCount, $page);

//        var_dump($pagination->page, $pagination->records_per_page);

        $records->orderBy($order,$direction);

        $records->forPage($pagination->page,$pagination->records_per_page);



        $return = new \stdClass();
        $return->list = (new Collection())->addFromIterable($records->get());
        $return->pagination = $pagination;


        $profiler->stop();
        return $return;
    }

    function get(?string $id = null): ?RolesModel {
        return $this->rolesModel->get($id)->getPermissions();
    }


}