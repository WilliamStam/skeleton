<?php


namespace App\Models;

use App\Model;

class SystemSessions extends AbstractModel {
    protected $primaryKey = 'session_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['session_id','data','user_key','session_id','ip','proxy_ip','agent'];

}