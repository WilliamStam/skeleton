<?php

namespace Modules\Admin;

use App\Middleware\AuthMiddleware;
use Modules\Admin\Permissions\AdminPermissions;
use Psr\Container\ContainerInterface;
use Slim\Routing\RouteCollectorProxy;
use System\Module\AbstractModule;
use System\Core\Permissions;


class Module extends AbstractModule {


    function moduleRoutes(RouteCollectorProxy $module): void {
        $module->group("/admin", function (RouteCollectorProxy $routes) {
            $routes->map(['GET','POST','DELETE'],"/roles[/{id}]", Controllers\RolesController::class)
                ->setName("admin_roles")
                ->add(new AuthMiddleware([
                    Controllers\RolesController::class,
                ])
            );
            $routes->map(['GET','POST','DELETE'],"/users[/{id}]", Controllers\UsersController::class)
                ->setName("admin_users")
                ->add(new AuthMiddleware([
                    Controllers\UsersController::class,
                ])
            );

        });

    }

    function moduleContainers(ContainerInterface $container): void {

    }

    function modulePermissions(ContainerInterface $container): void {
        $permissions = $container->get(Permissions::class);

        $permissions->add(Controllers\RolesController::class);
        $permissions->add(Controllers\UsersController::class);

        $permissions->add(array(
            "key"=>"test",
            "label"=>"Testing",
            "parent"=>AdminPermissions::class
        ));
        $permissions->add(array(
            "key"=>"test2",
            "label"=>"Testing2",
            "description"=>"la desc",
//            "parent"=>"p1",
            "parents"=>array(
                array(
                    "key"=>"p1",
                    "label"=>"P1",
                    "description"=>"P1 Desc"
                ),
                array(
                    "key"=>"p2",
                    "label"=>"P2",
                    "description"=>"P2 Desc"
                )

            )
        ));
        $permissions->add(array(
            "key"=>"test0",
            "label"=>"Testing0",
            "description"=>"la desc",
//            "parent"=>"p1",
            "parents"=>array(
                array(
                    "key"=>"p0",
                    "label"=>"P0",
                    "description"=>"P0 Desc"
                ),
                array(
                    "key"=>"p1",
                    "label"=>"P1",
                    "description"=>"P1 Desc"
                ),
                array(
                    "key"=>"p2",
                    "label"=>"P2",
                    "description"=>"P2 Desc"
                )

            )
        ));
        $permissions->add(array(
            "key"=>"test00",
            "label"=>"Testing000",
            "description"=>"la desc",
//            "parent"=>"p1",
            "parents"=>array(
                array(
                    "key"=>"p0",
                    "label"=>"P0",
                ),
                array(
                    "key"=>"p1",
                    "label"=>"P1",
                    "description"=>"P1 Desc"
                ),
                array(
                    "key"=>"p2",
                    "label"=>"P2",
                    "description"=>"P2 Desc"
                )

            )
        ));

        $permissions->add(array(
            "key"=>"test3",
            "label"=>"Testing3",
            "description"=>"la desc",
//            "parent"=>"p1",
            "parents"=>array(
                array(
                    "key"=>"p2",
                    "label"=>"P2",
                    "description"=>"P2 Desc"
                )

            )
        ));

    }


}