<?php

use DI\ContainerBuilder;
use Slim\App;

use System\Slim\InvocationStrategy;
//use System\Responders\Responder;

use System\Core\System;


require_once __DIR__ . '/../vendor/autoload.php';

$containerBuilder = new ContainerBuilder();

// Add container definitions
$containerBuilder->addDefinitions(__DIR__ . '/container.php');

$containerBuilder->useAutowiring(true);
$containerBuilder->useAnnotations(false);

//$containerBuilder->enableCompilation(__DIR__ . '/var/cache');

// Build PHP-DI Container instance
$container = $containerBuilder->build();

// Create App instance
$app = $container->get(App::class);

//$routeCollector = $app->getRouteCollector();
//$routeCollector->setDefaultInvocationStrategy(new InvocationStrategy(
//    $container->get(System::class)
//));



// Register middleware
(require __DIR__ . '/Middleware.php')($app);

// Register modules
(require __DIR__ . '/Modules.php')($app);

// Register routes
(require __DIR__ . '/Routes.php')($app);

return $app;