<?php


namespace Modules\Info\Controllers;

use App\Responders\Responder;
use App\Repositories\CurrentUserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use System\Core\Permissions;
use System\Core\System;
use System\Utilities\Arrays;
use System\Utilities\Strings;


class SystemController {

    function __construct(System $System, Responder $responder, CurrentUserRepository $userRepository, Permissions $permissions) {
        $this->system = $System;
        $this->responder = $responder;
        $this->userRepository = $userRepository;
        $this->permissions = $permissions;

    }

    /**
     * @OA\Get(
     *     path="/api/info",
     *     @OA\Response(response="200", description="get system info")
     * )
     */
    public function __invoke(Request $request, Response $response): Response {
        $errors = array();
        $messages = array();
        $data = array();

        $data['version'] = $this->system->get("VERSION");


        return $this->responder->withJson($response, array(
            "status" => count($errors) ? $this->responder::STATUS_FAILED : $this->responder::STATUS_SUCCESS,
            "data" => $data,
            "messages" => $messages,
            "errors" => $errors
        ));
    }


}

