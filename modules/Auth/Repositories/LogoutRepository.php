<?php


namespace Modules\Auth\Repositories;

use App\DB;
use App\Models\AttemptsModel;
use App\Models\UserCurrentModel;
use Psr\Log\LogLevel;
use System\Core\Loggers;
use System\Core\Profiler;
use System\Core\Session;
use System\Core\System;
use System\Utilities\Strings;


class LogoutRepository {
    protected $session;

    function __construct(System $System, DB $DB, Profiler $profiler, Loggers $loggers) {
        $this->system = $System;
        $this->DB = $DB;
        $this->profiler = $profiler;
        $this->loggers = $loggers;

    }


    public function logout($token) {
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);
        $return = null;


        $this->DB->exec("
            DELETE FROM system_authentication WHERE `token` = :TOKEN
        ",array(
            "TOKEN"=>$token,
        ));


        $profiler->stop();
        return $return;
    }



}