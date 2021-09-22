<?php


namespace Api\General\Controllers;

use App\Responders\Responder;
use Api\General\Schemas\UserSchema;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use System\Core\System;


class UserController {

    function __construct(System $System, Responder $responder, UserSchema $userSchema) {
        $this->system = $System;
        $this->responder = $responder;
        $this->userSchema = $userSchema;

    }

    /**
     * @OA\Get(
     *     path="/api/user",
     *     @OA\Response(response="200", description="Get the current user info")
     * )
     */
    public function __invoke(Request $request, Response $response): Response {
        $data = array();

        if ($request->getAttribute("USER") && $request->getAttribute("USER")->id()) {
            $data['user'] = $request->getAttribute("USER")->toSchema($this->userSchema);
            $data['permissions'] = array();
            $data['permissions'][] = "test.perm.1";
            $data['permissions'][] = "test.perm.2";

        }

        return $this->responder->withJson($response, $data);
    }


}

