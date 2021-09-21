<?php

namespace Modules\Auth\Models\Providers;

use App\Models\CurrentUserModel;
use System\Core\Profiler;
use System\Model\AbstractModel;
use System\Model\QueryTrait;
use System\Core\Collection;
use System\DB\Query;


class LDAPLogin implements LoginProviderInterface {
    protected $DB;
    protected $profiler;

    function __construct(\App\DB $DB, Profiler $profiler) {
         $this->DB = $DB;
        $this->profiler = $profiler;
    }


    public function __invoke(string $username, string $password) : ?CurrentUserModel {
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);

        $return = null;



        $profiler->stop();
        return $return;
    }


}