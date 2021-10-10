<?php


namespace App\Models;

use App\Model;

class SystemLogs extends AbstractModel {
    protected $fillable = ['version','level','log','context'];

}