<?php


namespace Modules\Admin\Repositories;

use App\DB;
use App\Models\SystemUsers;
use App\Models\SystemUsersRoles;

use System\Helpers\Collection;
use System\Core\Permissions;
use System\Core\Profiler;
use System\Core\System;

use System\Helpers\Pagination;


class UsersRepository {
    protected $session;
    protected $details = null;

    function __construct(System $System, Profiler $profiler) {
        $this->system = $System;
        $this->profiler = $profiler;
    }

    function list(
        ?string $search = null,
        int $page = 1,
        string $order = "name",
        string $direction = "asc",
        int|false $paginate = 30
    ) {
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);


        $records = SystemUsers::query();

//        $records->addSelect(DB::raw('1,2 as roles'));

        if ($search) {
            $search = '%' . $search . '%';
            $records->where("name", 'LIKE', $search);
            $records->orWhere("email", 'LIKE', $search);
        }
         $records->orderBy($order, $direction);
        $records->select()->addSelect(DB::raw("(SELECT GROUP_CONCAT(role_id) FROM system_users_roles WHERE system_users_roles.user_id = system_users.id) as roles"));

        if ($paginate){
    //        var_dump($records->toSql());
            $recordCount = $records->count();
    //        var_dump($recordCount);
            $pagination = new Pagination($paginate, 10);
            $pagination->calculate($recordCount, $page);

            $records->forPage($pagination->page, $pagination->records_per_page);


            $return = new \stdClass();
            $return->list = new Collection($records->get());
            $return->pagination = $pagination;

        } else {
            $return = new Collection($records->get());
        }


        $profiler->stop();
        return $return;
    }

    function get(?string $id = null) {
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);
//        if (!$this->details || $this->details->id != $id) {
        $this->details = SystemUsers::query()->findOrNew($id);
//        }
        $profiler->stop();
        return $this->details;
    }

    function roles(?SystemUsers $user = null) {
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);
        if (!$user) {
            $user = $this->details;
        }
        $roles = array();
        if ($user && $user->id) {
            $roles = \App\DB::table('system_roles')
                ->join('system_users_roles', 'system_users_roles.role_id', '=', 'system_roles.id')
                ->where('system_users_roles.user_id', '=', $user->id)
                ->get();
        }

        $profiler->stop();
        return new Collection($roles);
    }

    function save(array $values = array(), ?SystemUsers $user = null): SystemUsers {
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);
        if (!$user) {
            $user = $this->details;
        }

        if (array_key_exists("password", $values) && $values['password']) {
            $values['password'] = password_hash($values['password'], PASSWORD_DEFAULT);
            $values['salt'] = uniqid('', true);
        }

        $this->details = $user->updateOrCreate(
            ["id" => $user->id],
            $values
        );

        $details_id = $this->details->id;


        if (array_key_exists("roles",$values)){
            $existing_roles = $this->roles($this->details);
            $new_roles = (array) $values['roles'];

//            $this->system->debug($existing_roles->id->toArray(),$new_roles);

            $delete = SystemUsersRoles::query()
                ->where('id','is null')
            ;

            foreach ($existing_roles as $item) {
                if (!in_array($item->role_id, $new_roles)) {
                    $delete->orWhere(function($query) use ($item,$details_id){
                        $query->where('role_id', $item->role_id)
                            ->Where('user_id', $details_id );
                    });
                }
            }

            if ($delete->count()){
                $delete->delete();
            }

            $insert = array();


            $existing_perms = $existing_roles->role_id->toArray();
            foreach ($new_roles as $item) {
                // ADD ROLES
                if (!in_array($item, (array)$existing_perms)) {
                    $insert[] = array(
                        "user_id"=>$this->details->id,
                        "role_id"=>$item
                    );

                }
            }

            if (count($insert)){
                SystemUsersRoles::insert($insert);
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

    function validate(array $values = array(), ?SystemUsers $user = null): array {
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);
        if (!$user) {
            $user = $this->details;
        }
        $errors = array();
        if (array_key_exists("name", $values)) {
            if ($values['name'] == "") {
                $errors['name'][] = "Name is required";
            }
        }
        if (array_key_exists("email", $values)) {
            if (($values['email'] == "")) {
                $errors['email'][] = "Email is required";
            }


            if (filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {

                $email_in_use = SystemUsers::query()
                    ->where('email','=',$values['email'])
                    ->where('id','!=', $user->id)
                    ->count();
                // System::debug($this->system->get("DB")->log());
                if ($email_in_use != 0) {
                    $errors['email'][] = "The email address is already in use";
                }
            } else {
                $errors['email'][] = "Invalid email format";
            }

        }

        if (!$user->id){
            if (!isset($values['password']) || $values['password']==''){
                $errors['password'][] = "Password is required";
            }
        }

        if (array_key_exists("roles", $values)) {
            if (!is_array($values['roles'])) {
                $errors['roles'][] = "Roles must be an array of ids";
            }
        }



        $profiler->stop();
        return $errors;
    }

    function delete(?SystemUsers $user = null) {
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);
        $return = false;
        if (!$user) {
            $user = $this->details;
        }
        if ($user->id) {

            $user->delete();
            $return = true;

            SystemUsersRoles::query()
                ->where('user_id', '=', $user->id)
                ->delete()
            ;

        }


        $profiler->stop();
        return $return;
    }
}
