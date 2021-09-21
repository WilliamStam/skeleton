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


class LoginRepository {
    protected $session;

    function __construct(System $System, DB $DB, Profiler $profiler, Loggers $loggers, AttemptsModel $attemptsModel) {
        $this->system = $System;
        $this->DB = $DB;
        $this->profiler = $profiler;
        $this->loggers = $loggers;
        $this->attemptsModel = $attemptsModel;

        $this->session = null;

    }
    public function setSession(string $session){
        $this->session = $session;
        return $this;
    }

    public function login(string $username, string $password) {
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);
        $return = null;

        $logger = array(
            LogLevel::WARNING,
            "Login Unsuccessful",
            array(
                "username"=>$username,
                "password"=>Strings::maskString($password),
                "ip"=>$this->system->ip(),
                "agent"=>$this->system->agent(),
            )
        );

        foreach (array(
            new \Modules\Auth\Models\Providers\LDAPLogin($this->DB,$this->profiler),
            new \Modules\Auth\Models\Providers\LocalLogin($this->DB,$this->profiler),
        ) as $provider){

            $user = $provider($username,$password);
            if ($user && $user->id()){
                $return = $user;
                $logger = array(
                    LogLevel::INFO,
                    "Login Successful [{$user->id()}]",
                    array(
                        "user_id"=>$user->id(),
                        "provider"=>get_class($provider),
                        "ip"=>$this->system->ip(),
                        "agent"=>$this->system->agent(),
                    )
                );
                break;
            }

        }

        if ($user && $user->id()){
            // on successful login we clear the attempts
//            $this->attemptsModel->clear($this->session,"AUTH")
        } else {
            // on failed login we add an attempt
            $this->attemptsModel->add($this->session,"AUTH",$logger);
        }

        $this->loggers->getByName("auth")->log(...$logger);

        $profiler->stop();
        return $return;
    }
    public function attempts($minutes=5){
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);

        $return = $this->attemptsModel->count($this->session,"AUTH",$minutes);



        $profiler->stop();
        return $return;
    }

    public function generateAndSaveToken(CurrentUserModel $user) : string {
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);
        $bytes = random_bytes(20);
        $token = (bin2hex($bytes));



        $this->DB->exec("
            INSERT INTO system_authentication (
                `token`,
                `user_id`,
                `changed`
            ) VALUES (
                :TOKEN,
                :USER,
                '0'
            ) ON DUPLICATE KEY UPDATE 
                `user_id` = VALUES(`user_id`)
        ",array(
            "USER"=>$user->id(),
            "TOKEN"=>$token,
        ));




        $profiler->stop();
        return $token;
    }


}