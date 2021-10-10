<?php


namespace App\Controllers;

use App\Models\SystemRoles;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use System\Core\System;

use App\Responders\Responder;


class VueController {

    function __construct(System $system,Responder $responder) {
        $this->system = $system;
        $this->responder = $responder;
    }

    public function __invoke(Request $request, Response $response): Response {
        $GLOBALS['output'](get_class($this) . "");
        $data = array();

        $data['debug'] = $this->system->get("DEBUG");
//        $data['session'] = $request->getAttribute("SESSION")->getId();





        return $this->responder->withTemplate($response,"index.html",$data);
    }


}

