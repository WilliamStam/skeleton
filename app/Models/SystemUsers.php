<?php


namespace App\Models;

use App\Model;

class SystemUsers extends AbstractModel {

    protected $fillable = ['name','email','password','salt','settings','active', 'active_at'];

}