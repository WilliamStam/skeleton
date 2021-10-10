<?php


namespace Modules\Admin\Controllers;


use App\Responders\Responder;

use Modules\Admin\Permissions\AdminPermissions;
use Modules\Admin\Repositories\RolesRepository;
use Modules\Admin\Repositories\UsersRepository;
use Modules\Admin\Schemas\RolesDetailsSchema;
use Modules\Admin\Schemas\RolesListSchema;
use Modules\Admin\Schemas\PaginationSchema;

use Modules\Admin\Schemas\UserListSchema;
use Modules\Admin\Schemas\UsersDetailsSchema;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotImplementedException;
use System\Core\Permissions;
use System\Core\System;
use System\Core\Profiler;

use System\Permissions\PermissionsTrait;
use System\Utilities\Arrays;

class UsersController {
    use PermissionsTrait;

    const META = array(
        "key" => "users",
        "label" => "Admin Users",
        "description" => "Admin the system users",
    );
    const PARENT = AdminPermissions::class;


    function __construct(System $System, Responder $responder, Profiler $profiler, UsersRepository $usersRepository, RolesRepository $rolesRepository) {
        $this->system = $System;
        $this->profiler = $profiler;
        $this->responder = $responder;
        $this->usersRepository = $usersRepository;
        $this->rolesRepository = $rolesRepository;

    }

    public function __invoke(Request $request, Response $response, $args): Response {

        $callMethod = null;
        $methodUsed = $request->getMethod();

        if ($methodUsed == "GET") {
            if (array_key_exists("id", $args)) {
                $callMethod = "details";
            } else {
                $callMethod = "list";
            }
        } else {
            $callMethod = strtolower($methodUsed);
        }
        if (!method_exists($this, $callMethod)) {
            throw new HttpNotImplementedException($request);
        }

        return $this->{$callMethod}($request, $response, $args);

    }


    /**
     * Admin Roles
     * @OA\Get(
     *     path="/api/admin/users",
     *     @OA\Response(response="200", description="Admin Users")
     * )
     */


    public function list(Request $request, Response $response): Response {
        $errors = array();
        $messages = array();
        $data = array();


        list($column, $direction) = explode("|", $this->system->get("GET.order"));
        if (!in_array($column, array('name', 'email','active_at','updated_at'))) {
            $column = 'name';
        }
        if (!in_array($direction, array('asc', 'desc'))) {
            $direction = 'asc';
        }



        $data['setup'] = array(
            "search" => $this->system->get("GET.search"),
            "page" => $this->system->get("GET.page"),
            "order" => $column,
            "direction" => $direction
        );



        $list = $this->usersRepository->list(
            search: $data['setup']['search'],
            page: $data['setup']['page'],
            order: $column,
            direction: $direction
        );

        $data['list'] = $list->list->toSchema(new UserListSchema());
        $data['pagination'] = $list->pagination->result();


        $data['roles'] =  $this->rolesRepository->list(paginate:false)->toSchema(new RolesListSchema());



        return $this->responder->withJson($response, array(
            "status" => count($errors) ? $this->responder::STATUS_FAILED : $this->responder::STATUS_SUCCESS,
            "data" => $data,
            "messages" => $messages,
            "errors" => $errors
        ));
    }

    public function details(Request $request, Response $response): Response {

        $errors = array();
        $messages = array();
        $data = array();

        $details = $this->usersRepository->get($this->system->get("PARAMS.id"));


//        $permissions = $this->usersRepository->permissions()->withFilter(function($item,$permissions){
//            return in_array($item->permission,$permissions);
//        },$this->permissions->id->toArray());

        $data['details'] = $details->toSchema(new UsersDetailsSchema());

        $roles = $this->rolesRepository->list(paginate:false);

//        $this->system->debug($this->usersRepository->roles($details)->role_id->toArray());

        $data['roles'] = $roles->toSchema(new RolesListSchema($this->usersRepository->roles($details)->role_id->toArray()));




        return $this->responder->withJson($response, array(
            "status" => count($errors) ? $this->responder::STATUS_FAILED : $this->responder::STATUS_SUCCESS,
            "data" => $data,
            "messages" => $messages,
            "errors" => $errors
        ));
    }

    // save
    public function post(Request $request, Response $response): Response {
        $errors = array();
        $messages = array();
        $data = array();

        $record = $this->usersRepository->get($this->system->get("PARAMS.id"));

        $values = array(
            "name" => $this->system->get("POST.name"),
            "email" => $this->system->get("POST.email"),
            "roles" => $this->system->get("POST.roles"),
        );
        if ( $this->system->get("POST.password")){
            $values['password'] =  $this->system->get("POST.password");
        }


        $errors = $this->usersRepository->validate($values);


        if (!count($errors)) {
            $record = $this->usersRepository->save($values);

            $data['id'] = $record->id;

        }
        return $this->responder->withJson($response, array(
            "status" => count($errors) ? $this->responder::STATUS_FAILED : $this->responder::STATUS_SUCCESS,
            "data" => $data,
            "messages" => $messages,
            "errors" => $errors
        ));
    }


    // delete
    public function delete(Request $request, Response $response): Response {

        $errors = array();
        $messages = array();
        $data = array();

        $record = $this->usersRepository->get($this->system->get("PARAMS.id"));

        if (!$record->id) {
            $messages[] = array(
                "type" => "danger",
                "message" => "Record not found"
            );
            $errors['id'][] = "Record with that id not found";
        }

        if (!count($errors)) {
            $this->usersRepository->delete();
        }

        return $this->responder->withJson($response, array(
            "status" => count($errors) ? $this->responder::STATUS_FAILED : $this->responder::STATUS_SUCCESS,
            "data" => $data,
            "messages" => $messages,
            "errors" => $errors
        ));
    }


}

