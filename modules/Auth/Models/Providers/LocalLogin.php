<?php

namespace Modules\Auth\Models\Providers;

use App\Models\SystemUsers;
use System\Core\Profiler;
use System\Model\AbstractModel;
use System\Core\Collection;


class LocalLogin implements LoginProviderInterface {
    protected $profiler;

    function __construct( Profiler $profiler) {
        $this->profiler = $profiler;
    }


    public function __invoke(string $username, string $password): ?SystemUsers {
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);

        $return = null;

        $users = SystemUsers::query()
            ->where("email","=",strtolower($username))
            ->get()
        ;


        foreach ($users as $user) {
            if ($user && $user->id) {
                $result = password_verify($password, $user->password);
                if ($result) {
                    $return = $user;
                    break;
                }
            }
        }


        $profiler->stop();
        return $return;
    }


}