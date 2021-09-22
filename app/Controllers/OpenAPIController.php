<?php


namespace App\Controllers;

use App\Schemas\TestSchema;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\TestModel;
use Slim\Views\PhpRenderer;
use System\Core\Profiler;
use System\Core\Settings;
use System\Core\Modules;
use System\Core\Loggers;
use System\Core\System;
use System\Core\Session;

use App\Repositories\TestRepository;
use System\DB\Mysql;
use System\Files\Handlers\Image2;
use System\Loggers\FunctionLogger;
use System\Module\ModuleInterface;
use App\Responders\Responder;

use Psr\Http\Message\ResponseFactoryInterface;
use System\Utilities\Strings;


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


        $api_sources = array(
            $this->system->get("ROOT") . DIRECTORY_SEPARATOR . "api",
            $this->system->get("ROOT") . DIRECTORY_SEPARATOR . "app"
        );

        $openapi = \OpenApi\Generator::scan($api_sources);
        $spec = json_decode(($openapi->toJson()),true);
        $spec['info']['title'] = $this->system->get("PACKAGE");
        $spec['info']['version'] = $this->system->get("VERSION");

//         $this->system->debug($spec);
        $data['spec'] = ($spec);


//        $this->system->debug($data);


        return $this->responder->withTemplate($response,"OpenAPI.php",$data,Strings::fixDirSlashes($this->system->get("ROOT")."/app/Views"));
    }

}

