<?php


namespace Modules\System;

use App\Responders\Responder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use System\Core\System;


class InfoController {
    function __construct(System $System, Responder $responder) {
        $this->system = $System;
        $this->responder = $responder;
    }

    public function __invoke(Request $request, Response $response): Response {


        $data = array();
        $data['version'] = $this->system->get("VERSION");
        $data['debug'] = $this->system->get("DEBUG");




        return $this->responder->withJson($response, $data);
    }


}