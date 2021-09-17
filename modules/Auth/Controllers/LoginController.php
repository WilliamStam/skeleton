<?php


namespace Modules\Auth\Controllers;

use App\DB;
use App\Models\TestModel;
use App\Repositories\TestRepository;
use App\Responders\Responder;
use App\Schemas\TestSchema;
use Modules\Auth\LoginModel;
use Modules\Auth\Repositories\LoginRepository;
use Modules\Auth\Schemas\UserSchema;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LogLevel;
use System\Core\Profiler;
use System\Core\Session;
use System\Core\System;


class LoginController {

    function __construct(System $System, Responder $responder, LoginRepository $LoginRepository, UserSchema $userSchema) {
        $this->system = $System;
        $this->responder = $responder;
        $this->LoginRepository = $LoginRepository;
        $this->userSchema = $userSchema;

    }


    public function get(Request $request, Response $response): Response {
        $this->LoginRepository->setSession($request->getAttribute("SESSION")->id());
        $data = array();
        $data['active'] = false;
        $data['messages'] = array();

        if ($request->getAttribute("USER") && $request->getAttribute("USER")->id()) {
            $data['messages'][] = array(
                "type" => "success",
                "message" => "You are already logged in."
            );
        } else {
            if ($this->LoginRepository->attempts($this->system->get("SETTINGS.auth.minutes")) < $this->system->get("SETTINGS.auth.attempts")) {
                $data['messages'][] = array(
                    "type" => "warning",
                    "message" => "Use your full email address and network password (same as you use to log into windows)."
                );
                $data['active'] = true;
            } else {
                $data['messages'][] = array(
                    "type" => "error",
                    "message" => "Too many attempts. Try again later."
                );
            }
        }


        return $this->responder->withJson($response, $data);
    }

    public function post(Request $request, Response $response): Response {

        $this->LoginRepository->setSession($request->getAttribute("SESSION")->id());

        $data = array();
        $data['active'] = false;
        $data['username'] = $this->system->get("POST.username");
        $data['messages'] = array();

        $user = null;


        // if login attempts in less than set value then we good to try again
        if ($this->LoginRepository->attempts($this->system->get("SETTINGS.auth.minutes")) < $this->system->get("SETTINGS.auth.attempts")) {
            $user = $this->LoginRepository->login($this->system->get("POST.username"), $this->system->get("POST.password"));

            if ($user && $user->id()) {
                $data['messages'][] = array(
                    "type" => "success",
                    "message" => "Login successful"
                );


                $data['token'] = $this->LoginRepository->generateAndSaveToken($user);
                $data['user'] = $user->toSchema($this->userSchema);
                //            $data['user'] = $user->toSchema()

                return $this->responder->withJson($response, $data);
            }
        }


        // user logged in


        $attempts = $this->LoginRepository->attempts($this->system->get("SETTINGS.auth.minutes")) * 1;
        $remaining = ($this->system->get("SETTINGS.auth.attempts") * 1) - $attempts;

        if ($remaining == 0) {
            $data['messages'][] = array(
                "type" => "error",
                "message" => "Too many attempts. Try again later."
            );
        } else {
            $data['messages'][] = array(
                "type" => "error",
                "message" => "Login unsuccessful. {$remaining} Attempts remaining"
            );
            $data['active'] = true;
        }
        // login failed

        return $this->responder->withJson($response, $data);


    }


}

