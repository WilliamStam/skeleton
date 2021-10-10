<?php


namespace Modules\Auth\Repositories;

use App\DB;
use App\Models\SystemUsers;
use App\Models\SystemAttempts;
use App\Models\SystemSessions;
use App\Models\SystemAuthentication;
use Psr\Log\LogLevel;
use System\Core\Loggers;
use System\Core\Profiler;
use System\Core\Session;
use System\Core\System;
use System\Utilities\Info;
use System\Utilities\Strings;


class LoginRepository {
    protected $session;

    function __construct(System $System, Profiler $profiler, Loggers $loggers) {
        $this->system = $System;
        $this->profiler = $profiler;
        $this->loggers = $loggers;

        $this->session = null;

    }

    public function setSession(Session $session) {
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
                "username" => $username,
                "password" => Strings::maskString($password),
                "ip" => Info::ip(),
                "proxy_ip" => Info::proxy_ip(),
                "agent" => Info::agent(),
            )
        );


        foreach (array(
            new \Modules\Auth\Models\Providers\LDAPLogin($this->profiler),
            new \Modules\Auth\Models\Providers\LocalLogin($this->profiler),
        ) as $provider) {

            $user = $provider($username, $password);

            if ($user && $user->id) {
                $return = $user;
                $logger = array(
                    LogLevel::INFO,
                    "Login Successful [{$user->id}]",
                    array(
                        "user_id" => $user->id,
                        "provider" => get_class($provider),
                        "ip" => $this->system->ip,
                        "agent" => $this->system->agent(),
                    )
                );
                break;
            }

        }


        if ($user && $user->id) {
            // on successful login we clear the attempts
//            $this->attemptsModel->clear($this->session,"AUTH")
        } else {
            // on failed login we add an attempt

            SystemAttempts::create(array(
                "identifier" => $this->session->id(),
                "type" => "AUTH",
                "ip" => Info::ip(),
                "proxy_ip" => Info::proxy_ip(),
                "agent" => Info::agent(),
                "payload" => json_encode($logger)
            ));

        }

        $this->loggers->getByName("auth")->log(...$logger);

        $profiler->stop();
        return $return;
    }

    public function attempts($minutes = 5) {
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);

        $return = SystemAttempts::query()
            ->where("identifier", "=", $this->session->id())
            ->where("type", "=", "AUTH")
            // `timestamp` >= DATE_SUB(now(), INTERVAL {$minutes} MINUTE)
            ->where("created_at", ">=", \App\DB::raw("DATE_SUB(now(), INTERVAL {$minutes} MINUTE)"))
            ->count();


        $profiler->stop();
        return $return;
    }

    public function generateAndSaveToken(SystemUsers $user): string {
        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);
        $bytes = random_bytes(20);
        $token = (bin2hex($bytes));


        SystemAuthentication::updateOrCreate(
            ["token" => $token],
            ["token" => $token,"user_id" => $user->id],
        );

//        $user_key = $user->id ."|". $user->salt;

//        SystemSessions::upsert(
//
//            ['session_id' => $this->session->id(), 'user_key' => $user_key],
//            ['session_id' => $this->session->id()],
//            ['user_key' => $user_key],
//        );

//        $this->DB->exec("
//            INSERT INTO system_authentication (
//                `token`,
//                `user_id`,
//                `changed`
//            ) VALUES (
//                :TOKEN,
//                :USER,
//                '0'
//            ) ON DUPLICATE KEY UPDATE
//                `user_id` = VALUES(`user_id`)
//        ",array(
//            "USER"=>$user->id(),
//            "TOKEN"=>$token,
//        ));


        $profiler->stop();
        return $token;
    }


}