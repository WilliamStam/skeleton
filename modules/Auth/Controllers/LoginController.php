<?php


namespace Modules\Auth\Controllers;

use App\Models\TestModel;
use App\Repositories\TestRepository;
use App\Responders\Responder;
use App\Schemas\TestSchema;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use System\Core\Profiler;
use System\Core\Session;
use System\Core\System;


class LoginController {

    function __construct(System $System, Responder $responder) {
        $this->system = $System;
        $this->responder = $responder;
    }

    public function __invoke(Request $request, Response $response): Response {


        $data = array();
        $data['id'] = $this->system->get("GET.id");
        $data['version'] = $this->system->get("VERSION");


        return $this->responder->withJson($response, $data);
    }


}