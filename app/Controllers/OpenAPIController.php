<?php


namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use System\Core\Profiler;
use System\Core\System;

use App\Responders\Responder;

use System\Utilities\Strings;

/**
 * @OA\OpenApi(
 *    security={{"bearerAuth": {}}}
 * )
 *
 * @OA\Components(
 *     @OA\SecurityScheme(
 *         securityScheme="bearerAuth",
 *         type="http",
 *         scheme="bearer",
 *     )
 * )
 */

/**
 * @OA\Info(title="", version="")
 */




class OpenAPIController {

    function __construct(Profiler $profiler, System $System, Responder $responder) {
        $this->profiler = $profiler;
        $this->system = $System;
        $this->responder = $responder;
    }

    public function __invoke(Request $request, Response $response): Response {
        $GLOBALS['output'](get_class($this) . "");
        $data = array();

        $path = "{$request->getUri()->getScheme()}://{$request->getUri()->getHost()}/";

        $api_sources = array(
            $this->system->get("ROOT") . DIRECTORY_SEPARATOR . "modules",
            $this->system->get("ROOT") . DIRECTORY_SEPARATOR . "app"
        );

        $openapi = \OpenApi\Generator::scan($api_sources);
        $spec = json_decode(($openapi->toJson()),true);
        $spec['info']['title'] = $this->system->get("PACKAGE");
        $spec['info']['version'] = $this->system->get("VERSION");
        $spec['servers'] = array(
            array(
                "url"=>"$path",
                "description"=>"Main API"
            )
        );

//         $this->system->debug($request->getUri());
//         $this->system->debug($path,$spec);
        $data['spec'] = ($spec);


//        $this->system->debug($data);


        return $this->responder->withTemplate($response,"OpenAPI.php",$data,Strings::fixDirSlashes($this->system->get("ROOT")."/app/Views"));
    }

}

