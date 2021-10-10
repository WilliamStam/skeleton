<?php


namespace Modules\Testing\Controllers;

use Modules\Testing\Models\TestModel;
use Modules\Testing\Schemas\TestSchema;


use App\Responders\Responder;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use System\Core\System;
use System\Core\Profiler;


use System\Module\ModuleInterface;

class TabsController {
    function __construct(System $System, Responder $responder, Profiler $profiler, TestModel $testmodel, TestSchema $testschema, ModuleInterface $module) {
        $this->system = $System;
        $this->profiler = $profiler;
        $this->responder = $responder;
        $this->testmodel = $testmodel;
        $this->testschema = $testschema;
        $this->module = $module;
    }
/**
     * @OA\Get(
     *     path="/api/test/tab",
     *     @OA\Response(response="200", description="Tab testing")
     * )
     */
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
        $data['csrf'] = $request->getAttribute("SESSION")->get("CSRF");
        $data['csrfs'] = array("@@CSRF_NAME@@"=>"@@CSRF@@");

        $data['module_using_request'] = array(
            "id"=>$request->getAttribute("MODULE")->id(),
            "path"=>$request->getAttribute("MODULE")->getPath()
        );

        $data['module_using_di'] = array(
            "id"=>$this->module->id(),
            "path"=>$this->module->getPath()
        );


        $data['list'] = $this->testmodel->getAll(["order"=>"id DESC"])->withFilter(function($item){
            return $item->active == "1"; // this filters after you already have a collection. this isnt filtering on the db side
        })->toSchema($this->testschema,$request->getAttribute("USER"));

        // getAll returns a clone of the origional Query
        $test = $this->testmodel->getAll(["order"=>"id DESC"]);
//        var_dump("getAll",json_encode($test->id()->toArray()));
        $test->save(array(
            "last_actives"=>date("Y-m-d H:i:s")
        )); // will update all the records returned


        // get returns a clone of the origional Query
        $test = $this->testmodel->get("1");
//        var_dump("get",json_encode($test->id()));
        $test->save(array(
            "last_active"=>date("Y-m-d H:i:s")
        )); // will update the singe record returned

        // since the model is still "pure" we can just keep using it to insert new records
        $test2 = $this->testmodel;
//        var_dump("insert",json_encode($test2->id()));
         $id = $test2->save(array(
            "last_active"=>date("Y-m-d H:i:s")
        ));

//        var_dump("inserted id",$id);



        $rec = $this->testmodel->insert(array(
            "id"=>"1",
            "name"=>"William Stam",
            "email"=>"william@munsoft.co.za",
            "password"=>"test",
            "active"=>"1",
        ),true);




//        $this->testmodel->delete("name is null");




        $data['headers'] = $request->getHeaders();

        $profiler = $this->profiler->start("test label","test component");
//        sleep(1);
//
        $profiler->stop();
//        sleep(1);;

        return $this->responder->withJson($response, $data);
    }


}