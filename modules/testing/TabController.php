<?php


namespace Modules\testing;

use App\Models\TestModel;
use App\Responders\Responder;
use App\Schemas\TestSchema;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use System\Core\System;
use System\Core\Profiler;


class TabController {
    function __construct(System $System, Responder $responder, Profiler $profiler, TestModel $testmodel, TestSchema $testschema) {
        $this->system = $System;
        $this->profiler = $profiler;
        $this->responder = $responder;
        $this->testmodel = $testmodel;
        $this->testschema = $testschema;
    }

    public function __invoke(Request $request, Response $response): Response {


        $data = array();
        $data['tab'] = $this->system->get("PARAMS.tab");
        $data['r'] = $this->system->get("GET.r");
        $data['version'] = $this->system->get("VERSION");
        $data['date'] = date("Y-m-d H:i:s");
        $data['get'] = $this->system->get("GET");
        $data['post'] = $this->system->get("POST");

        $data['sid'] = $this->system->get("SESSION")->getId();
        $data['sid2'] = $request->getAttribute("SESSION")->getId();

        $data['logs'] = $this->testmodel->getAll()->withFilter(function($item){
            return $item->level == "error"; // this filters after you already have a collection. this isnt filtering on the db side
        })->toSchema($this->testschema,"2");





        $data['headers'] = $request->getHeaders();

        $profiler = $this->profiler->start("test label","test component");
//        sleep(1);
//
        $profiler->stop();
//        sleep(1);;

        return $this->responder->withJson($response, $data);
    }


}