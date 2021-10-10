<?php


namespace App\Models;

use App\Model;

class SystemAttempts extends AbstractModel {
    protected $fillable = ['identifier','type','ip','proxy_ip','agent','payload'];


}