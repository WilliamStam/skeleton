<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Interfaces\RouteParserInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Middleware\ErrorMiddleware;


//use Slim\Middleware\ErrorMiddleware;


use Slim\Psr7\Factory\StreamFactory;
use Slim\Views\PhpRenderer;


use System\Core\Media;
use System\Core\System;
use System\Core\Settings;
use System\Core\Profiler;
use System\Core\Modules;
use System\Core\Errors;
use System\Core\Loggers;
use System\Core\Session;
use System\Core\Replace;
use System\Core\Permissions;


use App\Responders\Responder;

use System\Utilities\Arrays;
use System\Utilities\Strings;

//use System\DB\Mysql;

use App\ErrorHandler;
use Psr\Log\LogLevel;

use System\Errors\Error;
use System\Loggers\FunctionLogger;
use App\Assets;

return [
    // Application settings
    'PACKAGE' => function () {
        $package = json_decode(file_get_contents("../composer.json"));

        return $package;
    },

    App::class => function (ContainerInterface $container) {
        AppFactory::setContainer($container);

        return AppFactory::create();
    },

    // HTTP factories
    StreamFactoryInterface::class => function (ContainerInterface $container) {
        return (new StreamFactory());
    },

    ResponseFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(App::class)->getResponseFactory();
    },

    // The Slim RouterParser
    RouteParserInterface::class => function (ContainerInterface $container) {
        return $container->get(App::class)->getRouteCollector()->getRouteParser();
    },

    RouteCollectorInterface::class => function (ContainerInterface $container) {
        return $container->get(App::class)->getRouteCollector();
    },


    // App containers

    // to override any of these values do so in ../config.php
    Settings::class => function (ContainerInterface $container) {
        // setting some php defaults
        error_reporting(0);
        ini_set('display_errors', '0');
        date_default_timezone_set('Africa/Johannesburg');

        $root = dirname(__DIR__);

        $storage = Strings::fixDirSlashes($root . DIRECTORY_SEPARATOR . "/storage");
        $assets = Strings::fixDirSlashes(realpath($root . "/../skeleton-vue/dist"));


        return (new Settings([
                "debug" => false,
                "storage" => $storage,
                "assets" => $assets,
                "media" => Strings::fixDirSlashes($storage . "/media"),
                "profiler" => array(
                    "add_headers" => false,
                    "output" => false
                ),
                "errors" => array(
                    // Should be set to false for the production environment
                    'display_error_details' => false,
                    // Should be set to false for the test environment
                    'log_errors' => true,
                    // Display error details in error log
                    'log_error_details' => true,
                ),
                "logs" => array(
                    "errors" => Strings::fixDirSlashes($storage . "/logs/errors")
                ),
                "db" => array(
                    "host" => null,
                    "port" => null,
                    "database" => null,
                    "username" => null,
                    "password" => null,
                    'flags' => [
                        // Turn off persistent connections
                        PDO::ATTR_PERSISTENT => false,
                        // Enable exceptions
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        // Emulate prepared statements
                        PDO::ATTR_EMULATE_PREPARES => true,
                        // Set default fetch mode to array
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        // Convert numeric values to strings when fetching.
                        // Since PHP 8.1 integers and floats in result sets will be returned using native PHP types.
                        // This option restores the previous behavior.
                        PDO::ATTR_STRINGIFY_FETCHES => true,
                    ],
                ),
                // session options
                'session' => array(
                    // session cookie name
                    'name' => 'sid',
                    // Lax will sent the cookie for cross-domain GET requests
                    'cookie_samesite' => 'Lax',
                    // Optional: Sent cookie only over https
                    'cookie_secure' => true,
                    // Optional: Additional XSS protection
                    // Note: The cookie is not accessible for JavaScript!
                    'cookie_httponly' => false,
                ),
                'csrf' => "csrf_test_name",
                'auth' => array(
                    // can fail 5 logins per 5 minutes
                    'attempts' => 5,
                    'minutes' => 5,
                ),

            ]
        ))->fromFile("../config.php");
    },


//    Profiler::class => function (ContainerInterface $container) {
//        return new Profiler();
//    },

    System::class => function (ContainerInterface $container) {
        $settings = $container->get(Settings::class);
        $package = $container->get("PACKAGE");
        return (new System([
                "ROOT" => realpath(__DIR__ . DIRECTORY_SEPARATOR . '../'),
                "DEBUG" => $settings->get("debug"),
                "VERSION" => $package->version,
                "PACKAGE" => $package->description,
                "SETTINGS" => $settings,
                "SESSION" => $container->get(Session::class),
                "GET" => $_GET,
                "POST" => $_POST
            ]
            , $container->get(Profiler::class)
        ));
    },

    Replace::class => function (ContainerInterface $container) {
        $settings = $container->get(Settings::class);
        $system = $container->get(System::class);
        $session = $container->get(Session::class);
        $replace = new Replace();
        $replace->set("@@PACKAGE@@", $container->get("PACKAGE")->description);
        $replace->set("@@VERSION@@", $container->get("PACKAGE")->version);
        $replace->set("@@CSRF_NAME@@", $settings->get("csrf"));
        $replace->set("@@CSRF@@", $session->get("CSRF"));

        return $replace;
    },
    Session::class => function (ContainerInterface $container) {
        $session = new Session(
            new \App\Handlers\Sessions\MySQLSessionHandler(),
            $container->get(Profiler::class),
        );
//
        $session->setName(Strings::toAscii($container->get("PACKAGE")->description));

        $session_options = Arrays::merge(array(
            "name"=>"SID",
            "gc_maxlifetime"=>"3600",
            "cookie_lifetime"=>"3600",
            "cookie_path"=>"/",
            "cookie_domain"=>"",
            "cookie_secure"=>"",
            "cookie_httponly"=>"",
            "cookie_samesite"=>"",
            "use_strict_mode"=>"0",
            "use_cookies"=>"1",
            "use_only_cookies"=>"1",
        ),$container->get(Settings::class)->get("session"));

        $session_options['cookie_secure'] = true;
        $session_options['cookie_samesite'] = 'None';
        $session_options['cookie_httponly'] = true;
        $session_options['cookie_domain'] = '';

        $session->setOptions($session_options);


        $session->set("CSRF", md5(uniqid(mt_rand(), true)));
        return $session;
    },


    Modules::class => function (ContainerInterface $container) {
        $modules = new Modules();
        return $modules;
    },
    Permissions::class => function (ContainerInterface $container) {
        $permissions = new Permissions();
        return $permissions;
    },

//    Modules::class => function (ContainerInterface $container) {
//        return new Modules();
//    },

//    ErrorMiddleware::class => function (ContainerInterface $container) {
//        $settings = $container->get('settings')['error'];
//        $app = $container->get(App::class);
//
//        $logger = $container->get(LoggerFactory::class)
//            ->addFileHandler('error.log')
//            ->createLogger();
//
//        $errorMiddleware = new ErrorMiddleware(
//            $app->getCallableResolver(),
//            $app->getResponseFactory(),
//            (bool)$settings['display_error_details'],
//            (bool)$settings['log_errors'],
//            (bool)$settings['log_error_details'],
//            $logger
//        );
//
//        $errorMiddleware->setDefaultErrorHandler($container->get(DefaultErrorHandler::class));
//
//        return $errorMiddleware;
//    },


    Errors::class => function (ContainerInterface $container) {
        $loggers = $container->get(Loggers::class);

        $errors = new Errors();

        $errors->addHandler(new Error(
            $loggers->getByLevel(\Psr\Log\LogLevel::DEBUG)->toArray(),
            array(
                "code" => 400
            ),
        ), 400);

        $errors->addHandler(new Error(
            $loggers->getByLevel(\Psr\Log\LogLevel::DEBUG)->toArray(),
            array(
                "code" => 401
            ),
        ), 401);

        $errors->addHandler(new Error(
            $loggers->getByLevel(\Psr\Log\LogLevel::DEBUG)->toArray(),
            array(
                "code" => 403
            ),
        ), 403);

        $errors->addHandler(new Error(
            $loggers->getByLevel(\Psr\Log\LogLevel::DEBUG)->toArray(),
            array(
                "code" => 404
            ),
        ), 404);

        $errors->addHandler(new Error(
            $loggers->getByLevel(\Psr\Log\LogLevel::NOTICE)->toArray(),
            array(
                "code" => 429,
                "message" => "Too Many Requests",
                "title" => "429 Too Many Requests",
                "description" => "The user has sent too many requests in a given amount of time."
            ),
        ), 429);


        $errors->setDefault(new Error(
            $loggers->getByLevel(\Psr\Log\LogLevel::ERROR)->toArray(),
            array(
                "code" => 500,
                "message" => "Internal Server Error",
                "title" => "500 Internal Server Error",
                "description" => "The server died"
            ),
            true
        ));


        return $errors;
    },

    Loggers::class => function (ContainerInterface $container) {
        $logger = new Loggers($container->get(System::class));
        $DBLogger = $container->get("DBLogger");
        $system = $container->get(System::class);

        $logger->addHandler(new FunctionLogger($DBLogger), \Psr\Log\LogLevel::EMERGENCY, array(
            \Psr\Log\LogLevel::EMERGENCY
        ));
        $logger->addHandler(new FunctionLogger($DBLogger), \Psr\Log\LogLevel::ALERT, array(
            \Psr\Log\LogLevel::ALERT
        ));
        $logger->addHandler(new FunctionLogger($DBLogger), \Psr\Log\LogLevel::CRITICAL, array(
            \Psr\Log\LogLevel::CRITICAL
        ));
        $logger->addHandler(new FunctionLogger($DBLogger), \Psr\Log\LogLevel::ERROR, array(
            \Psr\Log\LogLevel::ERROR
        ));
        $logger->addHandler(new FunctionLogger($DBLogger), \Psr\Log\LogLevel::WARNING, array(
            \Psr\Log\LogLevel::WARNING
        ));
        if ($system->get("DEBUG")) {
            $logger->addHandler(new FunctionLogger($DBLogger), \Psr\Log\LogLevel::NOTICE, array(
                \Psr\Log\LogLevel::NOTICE
            ));
            $logger->addHandler(new FunctionLogger($DBLogger), \Psr\Log\LogLevel::INFO, array(
                \Psr\Log\LogLevel::INFO
            ));
            $logger->addHandler(new FunctionLogger($DBLogger), \Psr\Log\LogLevel::DEBUG, array(
                \Psr\Log\LogLevel::DEBUG
            ));
        }


        $logger->addHandler(new FunctionLogger($DBLogger), "auth", array());


        return $logger;
    },

    "DBLogger" => function (ContainerInterface $container) {
//        $db = $container->get(\App\DB::class);
        $system = $container->get(System::class);

        return function ($level, $message, $context) use ($system) {
            \App\Models\SystemLogs::create(array(
               "version"=>$system->get("VERSION"),
               "level"=>$level,
               "log"=>$message,
               "context"=>json_encode($context,JSON_PRETTY_PRINT),
            ));
//            $db->exec("
//                INSERT INTO system_logs (
//                    `version`,`level`,`log`,`context`
//                ) VALUES (
//                    :VERSION,:LEVEL, :LOG,:CONTEXT
//                )
//            ",array(
//                ":VERSION"=> $system->get("VERSION"),
//                ":LEVEL"=> $level,
//                ":LOG"=> $message,
//                ":CONTEXT"=> json_encode($context,JSON_PRETTY_PRINT),
//            ));
        };
    },

    Media::class => function (ContainerInterface $container) {
        $media = new Media($container->get(ResponseFactoryInterface::class)->createResponse());

        $media->addHandler(new \System\Files\Handlers\Image());
        $media->addHandler(new \System\Files\Handlers\Text());

        return $media;
    },
    \App\Assets::class => function (ContainerInterface $container) {
        $media = new Media($container->get(ResponseFactoryInterface::class)->createResponse());

        $media->addHandler(new \System\Files\Handlers\Image());
        $media->addHandler(new \System\Files\Handlers\Text());
        $media->addHandler(new \System\Files\Handlers\Javascript());
        $media->addHandler(new \System\Files\Handlers\Css());

        return $media;
    },

    PhpRenderer::class => function (ContainerInterface $container) {
        $settings = $container->get(Settings::class);
//        var_dump($web);
//        exit();
        return new PhpRenderer($settings->get("assets"));
    },


//    \App\DB::class => function (ContainerInterface $container) {
//        $settings = $container->get(Settings::class);
//        return (new \App\DB($container->get(Profiler::class)))->connect(
//            'mysql:host=' . $settings->get("db.host") . ":" . $settings->get("db.port") . ';dbname=' . $settings->get("db.database"),
//            $settings->get("db.username"),
//            $settings->get("db.password"),
//            $settings->get("db.flags")
//        );
//    },


];