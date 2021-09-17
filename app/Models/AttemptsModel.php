<?php

namespace App\Models;

use System\Core\Profiler;
use System\Core\System;
use System\Model\AbstractModel;
use System\Model\QueryTrait;
use System\Core\Collection;
use System\DB\Query;


class AttemptsModel {

    function __construct(\App\DB $DB, Profiler $profiler, System $system) {
        $this->DB = $DB;
        $this->profiler = $profiler;
        $this->system = $system;
    }


    function add(string $identifier,string $type,array $payload=array()) : int {
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);

        $this->DB->exec("
            INSERT INTO `system_attempts`(
                `identifier`, 
                `type`, 
                `ip`, 
                `agent`, 
                `payload`, 
                `timestamp`
            ) VALUES (
                 :IDENTIFIER,
                 :TYPE,
                 :IP,
                 :AGENT,
                 :PAYLOAD,
                 now()
            )
        ", array(
            ":IDENTIFIER" => $identifier,
            ":TYPE" => $type,
            ":IP" => $this->system->ip(),
            ":AGENT" => $this->system->agent(),
            ":PAYLOAD" => json_encode($payload)
        ));

        $profiler->stop();
        return $this->count($identifier,$type);
    }

    function count(string $identifier,string $type,int $minutes=5) : int {

        $return = $this->DB->exec("
            SELECT count(*) as c FROM  `system_attempts` WHERE `identifier` = :IDENTIFIER AND `type` = :TYPE AND `timestamp` >= DATE_SUB(now(), INTERVAL {$minutes} MINUTE)
        ", array(
            ":IDENTIFIER" => $identifier,
            ":TYPE" => $type,
        ))->first()['c'];;

        return $return;
    }

    function clear(string $identifier,string $type){

        $this->DB->exec("
           DELETE FROM  `system_attempts` WHERE `identifier` = :IDENTIFIER AND `type` = :TYPE
        ", array(
            ":IDENTIFIER" => $identifier,
            ":TYPE" => $type,
        ));


    }


}