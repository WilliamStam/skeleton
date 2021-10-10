<?php

namespace App\Handlers\Sessions;
use App\Models\SystemSessions;
use System\Utilities\Info;
use System\Sessions\SessionHandlerInterface;


class MySQLSessionHandler implements SessionHandlerInterface {


    public function __construct() {

    }



    public function read($session_id) : string {



        try {

            $session = SystemSessions::query()
                ->where("session_id","=",$session_id)
                ->get()
                ->first();


            return $session->data ?? "";


//            $session = $this->db->exec("SELECT * FROM system_sessions WHERE session_id = :SESSION",array(
//                "SESSION"=>$session_id
//            ))->first();
//            return $session['data'] ?? '';
        } catch (Exception $e) {
            return '';
        }

        return '';
    }

    public function write($session_id, $session_data) : bool {
        try {

//             var_dump($session_data);
//            exit();
            SystemSessions::upsert(

                ['session_id' => $session_id,'data'=>$session_data,"ip"=>Info::ip(),"proxy_ip"=>Info::proxy_ip(),"agent"=>Info::agent()],
                ['session_id' => $session_id],
                ["ip"=>Info::ip(),"proxy_ip"=>Info::proxy_ip(),"agent"=>Info::agent()],
            );

            return TRUE;
        } catch (Exception $e) {
            return FALSE;
        }
    }

    public function destroy($session_id) : bool {
        try {
             SystemSessions::destroy($session_id);
            return TRUE;
        } catch (Exception $e) {
            return FALSE;
        }
    }

    public function gc(int $max_lifetime) : int|false {
        try {
//            SystemSessions::destroy($session_id)

//                $users = User::where('updated_at', '>', \Carbon\Carbon::now()->subSeconds(5)->toDateTimeString())->get();

                $users = SystemSessions::where('updated_at', '<', \App\DB::raw('now'))->delete();
                var_dump($max_lifetime);
                var_dump($users);
                exit();

        } catch (Exception $e) {
            return FALSE;
        }
    }


}