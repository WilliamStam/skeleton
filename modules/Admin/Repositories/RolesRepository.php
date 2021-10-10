<?php


namespace Modules\Admin\Repositories;

use App\Models\SystemRoles;
use App\Models\SystemRolesPermissions;

use System\Helpers\Collection;
use System\Core\Permissions;
use System\Core\Profiler;
use System\Core\System;

use System\Helpers\Pagination;


class RolesRepository {
    protected $session;
    protected $details = null;

    function __construct(System $System, Profiler $profiler) {
        $this->system = $System;
        $this->profiler = $profiler;
    }

    function list(
        ?string $search = null,
        int $page = 1,
        string $order = "role",
        string $direction = "asc",
        int|false $paginate = 30
    ) {
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);


        $records = SystemRoles::query();

        if ($search) {
            $search = '%' . $search . '%';
            $records->where("role", 'LIKE', $search);
            $records->orWhere("description", 'LIKE', $search);
        }
        $records->orderBy($order, $direction);


        if ($paginate){
            $recordCount = $records->count();
    //        var_dump($recordCount);

            $pagination = new Pagination($paginate, 10);

            $pagination->calculate($recordCount, $page);


            $records->forPage($pagination->page, $pagination->records_per_page);


            $return = new \stdClass();
            $return->list = (new \System\Helpers\Collection())->addFromIterable($records->get());
            $return->pagination = $pagination;
        } else {
            $return = (new \System\Helpers\Collection())->addFromIterable($records->get());
        }
//


        $profiler->stop();
        return $return;
    }

    function get(?string $id = null) {
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);
//        if (!$this->details || $this->details->id != $id) {
        $this->details = SystemRoles::query()->findOrNew($id);
//        }
        $profiler->stop();
        return $this->details;
    }

    function permissions(?SystemRoles $role = null) {
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);
        if (!$role) {
            $role = $this->details;
        }
        $permissions = array();
        if ($role && $role->id) {
            $permissions = \App\DB::table('system_roles_permissions')
                ->select('system_roles_permissions.permission')
                ->where('system_roles_permissions.role_id', '=', $role->id)
                ->get();
        }

        $profiler->stop();
        return new Collection($permissions);
    }

    function save(array $values = array(), ?SystemRoles $role = null): SystemRoles {
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);
        if (!$role) {
            $role = $this->details;
        }

        $this->details = $role->updateOrCreate(
            ["id" => $role->id],
            $values
        );

        if (array_key_exists("permissions",$values)){
            $existing_permissions = $this->permissions($this->details);
            $new_permissions = (array) $values['permissions'];



//            $this->system->debug($new_permissions);

            $delete = SystemRolesPermissions::query()
                ->where('id','is null')

            ;

            foreach ($existing_permissions as $item) {
                // DELETE ROLES
                if (!in_array($item->permission, $new_permissions)) {
                    $delete->orWhere(function($query) use ($item){
                        $query->where('role_id', $this->details->id)
                            ->Where('permission', $item->permission);
                    });
                }
            }


            if ($delete->count()){
                $delete->delete();
            }




            $insert = array();


            $existing_perms = $existing_permissions->permission->toArray();
            foreach ($new_permissions as $item) {
                // ADD ROLES
                if (!in_array($item, (array)$existing_perms)) {
                    $insert[] = array(
                        "role_id"=>$this->details->id,
                        "permission"=>$item
                    );
//                    $this->DB->insert("system_roles_permissions",array(
//                        "role_id"=>$id,
//                        "permission"=>$item
//                    ),true);

                }
            }

            if (count($insert)){
                SystemRolesPermissions::insert($insert);
            }

//            $this->system->debug($add_to_array,$existing_perms,$new_permissions);

        }

//
//
//        $perms = array_map(function ($item) {
//            return ["permission" => $item, "role_id"=>$this->details->id];
//        },$values['permissions']);
//
//        $this->system->debug($perms);



//        SystemRolesPermissions::upsert(
//
//        )


        $profiler->stop();
        return $this->details;
    }

    function validate(array $values = array(), ?SystemRoles $role = null): array {
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);
        if (!$role) {
            $role = $this->details;
        }
        $errors = array();
        if (array_key_exists("role", $values)) {
            if ($values['role'] == "") {
                $errors['role'][] = "Role is required";
            }
        }
        if (array_key_exists("permissions", $values)) {
            if (!is_array($values['permissions'])) {
                $errors['permissions'][] = "Permissions must be an array of items";
            }
        }
        $profiler->stop();
        return $errors;
    }

    function delete(?SystemRoles $role = null) {
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);
        $return = false;
        if (!$role) {
            $role = $this->details;
        }
        if ($role->id) {

            $role->delete();
            $return = true;

            SystemRolesPermissions::query()
                ->where('role_id', '=', $role->id)
                ->delete()
            ;

        }


        $profiler->stop();
        return $return;
    }
}
//
//class SaveAble {
//    public function __construct(public SystemRoles $model ){
//
//    }
//
//    function save($values): SystemRoles {
//        return $this->model->updateOrCreate(
//            ["id" => $this->model->id],
//            $values
//        );
//    }
//
//    function validate(array $values=array()) : array {
//        $errors = array();
//        if (array_key_exists("role", $values)) {
//            if ($values['role'] == "") {
//                $errors['role'][] = "Role is required";
//            }
//        }
//        if (array_key_exists("permissions", $values)) {
//            if (!is_array($values['permissions'])) {
//                $errors['permissions'][] = "Permissions must be an array of items";
//            }
//        }
//
//        return $errors;
//    }
//
//
//
//}