<?php


namespace Modules\Testing\Controllers;

use Modules\Admin\Schemas\RolesListSchema;
use Modules\Testing\Models\TestModel;
use Modules\Testing\Repositories\RolesRepository;
use Modules\Testing\Schemas\TestSchema;


use App\Responders\Responder;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use System\Core\Collection;
use System\Core\System;
use System\Core\Profiler;


use System\Module\ModuleInterface;

use Modules\Testing\Models\RolesModel;

use App\DB;
use App\DB2;

class HomeController {
    function __construct(System $System, Responder $responder, Profiler $profiler, RolesRepository $rolesRepository) {
        $this->system = $System;
        $this->profiler = $profiler;
        $this->responder = $responder;
        $this->rolesRepository = $rolesRepository;
    }
    /**
     * Test Api endpoint!
     * @OA\Get(
     *     path="/api/test",
     *     @OA\Response(response="200", description="Testing testing test!")
     * )
     */
    /**
     * @OA\Response(
     *     response=200,
     *     description="aa",
     *     @OA\MediaType(
     *          mediaType="application/json",
     *          @OA\Schema(
     *              @OA\Property(property="name", type="integer", description="demo")
     *          ),
     *          @OA\Examples(example=200, summary="", value={"name":1}),
     *          @OA\Examples(example=300, summary="", value={"name":1}),
     *          @OA\Examples(example=400, summary="", value={"name":1})
     *     )
     *   )
     */

    public function __invoke(Request $request, Response $response): Response {


        $data = array();

//        $settings = $this->system->get("SETTINGS");

//
//        $capsule = new Capsule;
//        $capsule->addConnection([
//            'charset' => 'utf8',
//            'collation' => 'utf8_unicode_ci',
//            'prefix' => '',
//
//            "driver" => "mysql",
//            "host" => $settings->get("db.host"),
//            "database" => $settings->get("db.database"),
//            "username" => $settings->get("db.username"),
//            "password" => $settings->get("db.password")
//        ]);
//        $capsule->setAsGlobal();
//        $capsule->bootEloquent();


         $list = $this->rolesRepository->getList(page: "1",  order: "role", direction: "asc");

        $data['list'] = $list->list->toSchema(new RolesListSchema());
        $data['pagination'] = $list->pagination->result();


//        $elo = RolesModel::all();
//        $records = (new Collection())->addFromIterable($elo);
//
//        $this->system->debug(DB2::select("SELECT * FROM system_users"));
//        $this->system->debug($records->toSchema(new RolesListSchema()));
        $this->system->debug($data);


//        $data['t'] = RolesModel::all();;
        /*
         *
         * Running 30s test @ http://192.168.0.217:7251/api/test
  12 threads and 400 connections
  Thread Stats   Avg      Stdev     Max +/-Stdev
    Latency     1.21s   484.25ms   1.99s    57.89 %
        Req / Sec    20.35     19.66   101.00     81.06 %
        Latency Distribution
     50 % 1.30s
     75 % 1.64s
     90 % 1.77s
     99 % 1.99s
  2277 requests in 30.08s, 19.55MB read
  Socket errors: connect 0, read 0, write 0, timeout 2125
  Non - 2xx or 3xx responses: 2
Requests / sec:     75.69
Transfer / sec:    665.56KB
        */


//        $db = (new \App\DB($this->profiler))->connect(
//            'mysql:host=' . $settings->get("db.host") . ":" . $settings->get("db.port") . ';dbname=' . $settings->get("db.database"),
//            $settings->get("db.username"),
//            $settings->get("db.password"),
//            $settings->get("db.flags")
//        );
//        $data['t'] = $db->exec("SELECT * FROM system_users")->toArray();

        /*
         * $data['t'] = $this->DB->exec("SELECT * FROM system_users")->toArray();
         * Running 30s test @ http://192.168.0.217:7251/api/test
  12 threads and 400 connections
  Thread Stats   Avg      Stdev     Max   +/- Stdev
    Latency     1.14s   454.33ms   1.98s    56.99%
    Req/Sec    23.53     22.59   280.00     79.28%
  Latency Distribution
     50%    1.12s
     75%    1.51s
     90%    1.81s
     99%    1.98s
  2967 requests in 30.10s, 27.57MB read
  Socket errors: connect 0, read 0, write 0, timeout 2774
  Non-2xx or 3xx responses: 8
Requests/sec:     98.59
Transfer/sec:      0.92MB

         */


//        throw new \Slim\Exception\HttpInternalServerErrorException($request);

//        throw new \Slim\Exception\HttpForbiddenException($request,"go away");
//        throw new \Slim\Exception\HttpBadRequestException($request);

        return $this->responder->withJson($response, $data);
    }


}

