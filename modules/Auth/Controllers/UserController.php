<?php


namespace Modules\Auth\Controllers;

use App\DB;
use App\Models\TestModel;
use App\Repositories\TestRepository;
use App\Responders\Responder;
use App\Schemas\TestSchema;
use Modules\Auth\LoginModel;
use Modules\Auth\Repositories\LoginRepository;
use Modules\Auth\Schemas\UserSchema;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LogLevel;
use System\Core\Profiler;
use System\Core\Session;
use System\Core\System;


class UserController {

    function __construct(System $System, Responder $responder, UserSchema $userSchema) {
        $this->system = $System;
        $this->responder = $responder;
        $this->userSchema = $userSchema;

    }


    public function __invoke(Request $request, Response $response): Response {
        $data = array();

        if ($request->getAttribute("USER") && $request->getAttribute("USER")->id()){
            $data['user'] = $request->getAttribute("USER")->toSchema($this->userSchema);
            $data['permissions'] = array();
            $data['permissions'][] = "test.perm.1";
            $data['permissions'][] = "test.perm.2";

        }

        return $this->responder->withJson($response, $data);
    }


}

