<?php


namespace Modules\Auth\Repositories;

use App\Models\SystemAuthentication;
use Psr\Log\LogLevel;
use System\Core\Loggers;
use System\Core\Profiler;
use System\Core\Session;
use System\Core\System;
use System\Utilities\Strings;


class LogoutRepository {
    protected $session;

    function __construct(System $System, Profiler $profiler, Loggers $loggers) {
        $this->system = $System;
        $this->profiler = $profiler;
        $this->loggers = $loggers;

    }


    public function logout($token) {
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);
        $return = null;


        SystemAuthentication::destroy($token);



        $profiler->stop();
        return $return;
    }



}