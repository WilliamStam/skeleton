<?php

namespace App;

use Slim\App;
use System\Core\Settings;
use System\Exceptions\Schemas\NoSchemaPassed;
use System\Schema\SchemaInterface;


use Illuminate\Container\Container;



//class DB extends \Illuminate\Database\Capsule\Manager {}

return function (App $app) {

    class_alias("\Illuminate\Database\Capsule\Manager", 'App\DB');
//    class_alias("\Illuminate\Database\Eloquent\Model", 'App\DBModel');

    $container = $app->getContainer();
    $settings = $container->get(Settings::class);

    $capsule = new \App\DB();
    $capsule->addConnection([
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => '',

        "driver" => "mysql",
        "host" => $settings->get("db.host"),
        "database" => $settings->get("db.database"),
        "username" => $settings->get("db.username"),
        "password" => $settings->get("db.password")
    ]);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    \App\DB::connection()->enableQueryLog();
};

