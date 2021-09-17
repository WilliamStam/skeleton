<?php


namespace Modules\Auth\Controllers;

use Modules\Auth\Schemas\UserSchema;
use App\Models\TestModel;
use App\Models\UserCurrentModel;
use App\Repositories\TestRepository;
use App\Responders\Responder;
use App\Schemas\TestSchema;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use System\Core\Profiler;
use System\Core\Session;
use System\Core\System;


class AuthController {

    function __construct(System $System, Responder $responder, UserSchema $userSchema) {
        $this->system = $System;
        $this->responder = $responder;
        $this->userSchema = $userSchema;
    }

    public function __invoke(Request $request, Response $response): Response {


        $data = array();
        $data['token'] = $request->getAttribute("AUTHORIZATION");

        $user = $request->getAttribute("USER");

//        $this->system->debug($user->name);

        if ($user){
            $data['user'] = $request->getAttribute("USER")->toSchema($this->userSchema);
        }




        return $this->responder->withJson($response, $data);
    }


}