<?php

namespace App\Handlers\Sessions;
use System\Utilities\Info;
use System\Sessions\SessionHandlerInterface;

class MySQLSessionHandler implements SessionHandlerInterface {
    private $db;

    public function __construct($DB) {
        $this->db = $DB;
    }



    public function read($session_id) : string {
        try {
            $session = $this->db->exec("SELECT * FROM system_sessions WHERE session_id = :SESSION",array(
                "SESSION"=>$session_id
            ))->first();
            return $session['data'] ?? '';
        } catch (Exception $e) {
            return '';
        }
    }

    public function write($session_id, $session_data) : bool {
        try {

            $this->db->exec("
                INSERT INTO system_sessions (
                    `session_id`, 
                    `data`, 
                    `ip`, 
                    `proxy_ip`, 
                    `agent`, 
                    `timestamp`
                ) VALUES (
                    :SESSION,
                    :DATA,
                    :IP,
                    :PROXY_IP,
                    :AGENT,
                    UNIX_TIMESTAMP()
                ) ON DUPLICATE KEY UPDATE 
                    `data` = VALUES(`data`),
                    `ip` = VALUES(`ip`),
                    `proxy_ip` = VALUES(`proxy_ip`),
                    `agent` = VALUES(`agent`),
                    `timestamp` = VALUES(`timestamp`)
            
            ", array(
                "SESSION" => $session_id,
                "DATA" => $session_data,
                "IP" => Info::ip(),
                "PROXY_IP" => Info::proxy_ip(),
                "AGENT" => Info::agent(),
            ));

            return TRUE;
        } catch (Exception $e) {
            return FALSE;
        }
    }

    public function destroy($sessionId) : bool {
        try {
            $this->db->exec("DELETE FROM sessions WHERE session_id = :SESSION",array(
                "SESSION"=>$sessionId
            ));

            return TRUE;
        } catch (Exception $e) {
            return FALSE;
        }
    }


}