<?php

namespace Modules\Testing;
use Psr\Container\ContainerInterface;
use Slim\Routing\RouteCollectorProxy;
use System\Module\AbstractModule;


class Module extends AbstractModule {


    function moduleRoutes(RouteCollectorProxy $module): void {
        $module->group("/test", function (RouteCollectorProxy $group) {
           $group->map(["GET","POST"],"", Controllers\HomeController::class)->setName("testing_index");


        });

    }

    function moduleContainers(ContainerInterface $container) : void {

    }


}