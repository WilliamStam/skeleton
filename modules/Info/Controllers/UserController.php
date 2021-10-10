<?php


namespace Modules\Info\Controllers;

use App\Responders\Responder;
use App\Repositories\CurrentUserRepository;
use App\Schemas\CurrentUserSchema;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use System\Core\System;



class UserController {

    function __construct(System $System, Responder $responder, CurrentUserRepository $userRepository) {
        $this->system = $System;
        $this->responder = $responder;
        $this->userRepository = $userRepository;

    }

    /**
     * @OA\Get(
     *     path="/api/info/user",
     *     @OA\Response(response="200", description="get the current user")
     * )
     */
    public function __invoke(Request $request, Response $response): Response {
        $errors = array();
        $messages = array();
        $data = array();


         $data['user'] = null;
         $data['token'] = $request->getAttribute("TOKEN");
         if ($request->getAttribute("USER")){
             $data['user'] = $request->getAttribute("USER")->toSchema(new CurrentUserSchema(
                $request->getAttribute("PERMISSIONS")->toArray()
             ));
         } else {
             if ($request->getAttribute("TOKEN")){
                 $errors['token'][] = "Token either expired or not acceptable";
             } else {
                 $messages[] = array(
                     "type"=>"info",
                     "message"=>"No token supplied"
                 );
             }

         };




        return $this->responder->withJson($response, array(
            "status" => count($errors) ? $this->responder::STATUS_FAILED : $this->responder::STATUS_SUCCESS,
            "data" => $data,
            "messages" => $messages,
            "errors" => $errors
        ));
    }


}

