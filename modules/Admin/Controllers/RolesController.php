<?php


namespace Modules\Admin\Controllers;


use App\Responders\Responder;

use Modules\Admin\Permissions\AdminPermissions;
use Modules\Admin\Repositories\RolesRepository;
use Modules\Admin\Schemas\RolesDetailsSchema;
use Modules\Admin\Schemas\RolesListSchema;
use Modules\Admin\Schemas\PaginationSchema;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotImplementedException;
use System\Core\Permissions;
use System\Core\System;
use System\Core\Profiler;

use System\Permissions\PermissionsTrait;
use System\Utilities\Arrays;

class RolesController {
    use PermissionsTrait;

    const META = array(
        "key" => "roles",
        "label" => "Admin Roles",
        "description" => "Admin the system roles",
    );
    const PARENT = AdminPermissions::class;


    function __construct(System $System, Responder $responder, Profiler $profiler, RolesRepository $rolesRepository, Permissions $permissions) {
        $this->system = $System;
        $this->profiler = $profiler;
        $this->responder = $responder;
        $this->rolesRepository = $rolesRepository;
        $this->permissions = $permissions;

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
     *     path="/api/admin/roles",
     *     @OA\Response(response="200", description="Testing testing test!")
     * )
     */


    public function list(Request $request, Response $response): Response {
        $errors = array();
        $messages = array();
        $data = array();


        list($column, $direction) = explode("|", $this->system->get("GET.order"));
        if (!in_array($column, array('role', 'description'))) {
            $column = 'role';
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



        $list = $this->rolesRepository->list(
            search: $data['setup']['search'],
            page: $data['setup']['page'],
            order: $column,
            direction: $direction,
            paginate: 30
        );

        $data['list'] = $list->list->toSchema(new RolesListSchema());
        $data['pagination'] = $list->pagination->result();




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

        $details = $this->rolesRepository->get($this->system->get("PARAMS.id"));


        $permissions = $this->rolesRepository->permissions()->withFilter(function($item,$permissions){
            return in_array($item->permission,$permissions);
        },$this->permissions->id->toArray());

        $data['details'] = $details->toSchema(new RolesDetailsSchema($permissions->permission->toArray()));




//        $desc = $details->description ?? 0;

//         $details->save(array("role"=>"new role","description"=>$desc + 1, "permissions"=>array("fish.cakes","fish.fingers")));


        $permissions = array();
        foreach ($this->permissions as $item) {
            $parents = array_reverse($item->parents);
            $key = array();

            foreach ($parents as $parent) {
                $parent_key = $key;
                $key[] = $parent['key'];
                $check_key = (implode(".", $key));
//                 $this->system->debug($parent);


                if (!isset($permissions[$check_key])) {
                    $permissions[$check_key] = array(
                        "id" => $check_key,
                        "parent_id" => (implode(".", $parent_key)),
                        "label" => $parent['label'],
                        "description" => $parent['description'],
                        "children" => array(),
                        "items" => array()
                    );
                }
            }
        }

//        $this->system->debug($data['details']['permissions']);
        foreach ($this->permissions as $item) {
            $parent_id = array_map(function ($i) {
                return $i['key'];
            }, (array)array_reverse($item->parents));

            $parent_id = implode(".", $parent_id);

            $permissions[$parent_id]['items'][] = array(
                "id" => $item->id,
                "selected" => in_array($item->id, $data['details']['permissions']),
                "label" => $item->label,
                "description" => $item->description,
            );
        }


        $permissions = Arrays::hierarchy($permissions);
        $permissions = Arrays::hierarchyCounter($permissions);


        $data['permissions'] = $permissions;


//        sleep(1);


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

        $record = $this->rolesRepository->get($this->system->get("PARAMS.id"));

        $values = array(
            "role" => $this->system->get("POST.role"),
            "description" => $this->system->get("POST.description"),
            "permissions" => $this->system->get("POST.permissions"),
        );

        $data['param_id'] = $this->system->get("PARAMS.id");
        $data['details'] = $record->id;
        $data['values'] = $values;

        $errors = $this->rolesRepository->validate($values);


        if (!count($errors)) {
            $record = $this->rolesRepository->save($values);

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

        $record = $this->rolesRepository->get($this->system->get("PARAMS.id"));

        if (!$record->id) {
            $messages[] = array(
                "type" => "danger",
                "message" => "Record not found"
            );
            $errors['id'][] = "Record with that id not found";
        }

        if (!count($errors)) {
            $this->rolesRepository->delete();
        }

        return $this->responder->withJson($response, array(
            "status" => count($errors) ? $this->responder::STATUS_FAILED : $this->responder::STATUS_SUCCESS,
            "data" => $data,
            "messages" => $messages,
            "errors" => $errors
        ));
    }


}

