<?php


namespace Modules\Auth\Controllers;

use App\Models\TestModel;
use App\Repositories\TestRepository;
use App\Responders\Responder;
use App\Schemas\TestSchema;
use Modules\Auth\Repositories\LogoutRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use System\Core\Profiler;
use System\Core\Session;
use System\Core\System;


class LogoutController {

    function __construct(System $System, Responder $responder, LogoutRepository $logoutRepository) {
        $this->system = $System;
        $this->responder = $responder;
        $this->logoutRepository = $logoutRepository;
    }


    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     @OA\Response(response="200", description="Logout path")
     * )
     */

    public function __invoke(Request $request, Response $response): Response {


        $data = array();

        $this->logoutRepository->logout($request->getAttribute("TOKEN"));


        return $this->responder->withJson($response, $data);
    }


}