<?php


namespace App\Repositories;

use App\DB;
use App\Models\SystemUsers;

use System\Core\Profiler;
use System\Core\System;

use System\Permissions\PermissionHelper;
use System\Permissions\PermissionInterface;
use System\Utilities\Strings;
use System\Helpers\Collection;


class CurrentUserRepository {
    protected $model;

    function __construct(System $System, Profiler $profiler) {
        $this->system = $System;
        $this->profiler = $profiler;


    }


    function getByToken(?string $token_id = null) {
        $user = SystemUsers::query()
                ->join('system_authentications', 'system_users.id', '=', 'system_authentications.user_id')
                ->where("token", "=", $token_id)->get()->first() ?? null;
        $settings =  json_decode($user->settings,true) ?? array();
        $user->settings = new UserSettings((array)$settings);
        return $user;
    }

    function permissions(?SystemUsers $user) {
        $permissions = array();
        if ($user && $user->id) {
            $permissions = \App\DB::table('system_roles_permissions')
                ->select('system_roles_permissions.permission')
                ->join('system_roles', 'system_roles_permissions.role_id', 'system_roles.id')
                ->join('system_users_roles', 'system_users_roles.role_id', 'system_roles.id')
                ->where('system_users_roles.user_id', '=', $user->id)
                ->get();
        }

        return new PermissionCollection($permissions);
    }

    function save(?SystemUsers $user) {
        $t = clone $user;
        $t->timestamps = false;

        $t->update([
            "settings" => json_encode($user->settings->toArray()),
            "active_at" => DB::raw("now()")
        ]);
        //update(array("active_at" => DB::raw("now()")),array("touch"=>false));
//        $user->timestamps = true;
    }


}

class  UserSettings {
    protected $settings = array();
    function __construct(iterable $items){
        foreach ($items as $k=>$v){
            $this->set($k,$v);
        }
    }

    function set($key,$value){
        $key = Strings::toAscii($key);
        $this->settings[$key] = $value;
        return $this;
    }
    function get($key,$default = null){
        $key = Strings::toAscii($key);
        return $this->settings[$key] ?? $default;
    }

    function toArray(){
        return $this->settings;
    }
}

class PermissionCollection extends Collection {

    function hasPermissions($check_against = array()) {
        $return = array();

        if (is_string($check_against)) {
            $check_against = array($check_against);
        }

        $result = array();
        foreach ($check_against as $perm) {
            $key = false;
            // todo: should probably get the system permissions and check if the permission is in that first.
            // todo: to do that we would need to move the has permissions to a repo or something to sue DI to inject the permissions collection
            if ($perm instanceof PermissionInterface) {
                $key = Strings::toAscii($perm, ".");
            } else {
                if (is_string($perm)) {
                    $k = PermissionHelper::getMeta($perm);
                    if ($k && array_key_exists("id", $k)) {
                        $key = $k['id'];
                    }
                }
            }

            if ($key) {
                $result[$key] = false;
            }

        }
        foreach ($this->getCollection() as $item) {
            $key = $item->permission;
            if (in_array($key, array_keys($result))) {
                $result[$key] = true;
            }
        }
        $return = false;
        if (in_array(false, array_values($result), true) === false) {
            $return = true;
        }

        return $return;
    }

    function hasSomePermissions($check_against = array()) {
        $return = false;

        if (is_string($check_against)) {
            $check_against = array($check_against);
        }


        $result = array();
        foreach ((array)$check_against as $perm) {
            $key = Strings::toAscii($perm, ".");
            if (defined($perm . '::META')) {
                $meta = (new $perm)->getMeta();
                $key = $meta['id'];
            }
            $result[$key] = false;
        }
        foreach ($this->getCollection() as $item) {
            $key = $item->permission;
            if (in_array($key, array_keys($result))) {
                $result[$key] = true;
            }
        }


        $return = false;
        if (in_array(true, array_values($result), true)) {
            $return = true;
        }
        // System::debug($result,$return,$this->permissions);

        // $profiler->stop();
        return $return;
    }

}