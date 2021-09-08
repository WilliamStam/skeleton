<?php


namespace Modules\testing;

use App\Responders\Responder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use System\Core\System;
use System\Slim\Generic;


class HomeController {
    function __construct(System $System, Responder $responder) {
        $this->system = $System;
        $this->responder = $responder;
    }

    public function __invoke(Request $request, Response $response): Response {


        $data = array();
        $data['id'] = $this->system->get("PARAMS.id");
        $data['attributes'] = $request->getAttributes();
        $data['version'] = $this->system->get("VERSION");
        $data['route'] = $this->system->get("ROUTE")->getPattern();




        return $this->responder->withJson($response, $data);
    }


}