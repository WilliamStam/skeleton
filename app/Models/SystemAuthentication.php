<?php


namespace App\Models;

use App\Model;

class SystemAuthentication extends AbstractModel {
    protected $primaryKey = 'token';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['token','user_id'];

}