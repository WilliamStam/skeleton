<?php


namespace Modules\Auth\Repositories;

use App\DB;
use App\Models\AttemptsModel;
use App\Models\CurrentUserModel;
use Psr\Log\LogLevel;
use System\Core\Loggers;
use System\Core\Profiler;
use System\Core\Session;
use System\Core\System;
use System\Utilities\Strings;


class UserRepository {
    protected $session;

    function __construct(System $System, DB $DB, Profiler $profiler, Loggers $loggers, AttemptsModel $attemptsModel) {
        $this->system = $System;
        $this->DB = $DB;
        $this->profiler = $profiler;
        $this->loggers = $loggers;
        $this->attemptsModel = $attemptsModel;

        $this->session = null;

    }



}