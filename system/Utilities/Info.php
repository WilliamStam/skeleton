<?php

namespace System\Utilities;
class Info {


    static function ip() : ?string {
        return $_SERVER['REMOTE_ADDR'] ?? null;
    }
    static function proxy_ip() : ?string {
        return $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null;
    }

    static function agent() : ?string {
        return $_SERVER['HTTP_USER_AGENT']??null;
    }

}