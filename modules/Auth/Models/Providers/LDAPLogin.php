<?php

namespace Modules\Auth\Models\Providers;

use App\Models\SystemUsers;
use System\Core\Profiler;


class LDAPLogin implements LoginProviderInterface {
    protected $profiler;

    function __construct(Profiler $profiler) {
        $this->profiler = $profiler;
    }


    public function __invoke(string $username, string $password) : ?SystemUsers {
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);

        $return = null;



        $profiler->stop();
        return $return;
    }


}