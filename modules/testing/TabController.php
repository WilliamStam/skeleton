<?php


namespace Modules\testing;

use App\Responders\Responder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use System\Core\System;
use System\Core\Profiler;


class TabController {
    function __construct(System $System, Responder $responder, Profiler $profiler) {
        $this->system = $System;
        $this->profiler = $profiler;
        $this->responder = $responder;
    }

    public function __invoke(Request $request, Response $response): Response {


        $data = array();
        $data['tab'] = $this->system->get("PARAMS.tab");
        $data['r'] = $this->system->get("GET.r");
        $data['version'] = $this->system->get("VERSION");
        $data['date'] = date("Y-m-d H:i:s");
        $data['get'] = $this->system->get("GET");
        $data['post'] = $this->system->get("POST");
        $data['headers'] = $request->getHeaders();


        $profiler = $this->profiler->start("test 1");
        sleep(1);

        $profiler->stop();

        return $this->responder->withJson($response, $data);
    }


}