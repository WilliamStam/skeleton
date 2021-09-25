<?php


namespace Api\Auth\Controllers;

use App\DB;
use App\Responders\Responder;
use Api\Auth\LoginModel;
use Api\Auth\Repositories\LoginRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use System\Core\System;
use System\Core\Session;


class LoginController {

    function __construct(System $System, Responder $responder, LoginRepository $LoginRepository, Session $session) {
        $this->system = $System;
        $this->session = $session;
        $this->responder = $responder;
        $this->LoginRepository = $LoginRepository;

    }


    /**
     * @OA\Get(
     *     path="/api/auth/login",
     *     @OA\Response(response="200", description="Fetches current login status")
     * )
     */
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

            $attempts = $this->LoginRepository->attempts($this->system->get("SETTINGS.auth.minutes")) * 1;
            $remaining = ($this->system->get("SETTINGS.auth.attempts") * 1) - $attempts;

            if ($remaining <= 0) {
                $data['messages'][] = array(
                    "type" => "error",
                    "message" => "Too many attempts. Try again later."
                );
            } elseif ($attempts) {
                $data['messages'][] = array(
                    "type" => "warning",
                    "message" => "{$remaining} Attempts remaining"
                );
                $data['active'] = true;
            } else {
                $data['messages'][] = array(
                    "type" => "info",
                    "message" => "Use your full email address and network password (same as you use to log into windows)."
                );
                $data['active'] = true;
            }

        }


        return $this->responder->withJson($response, $data);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     @OA\Response(response="200", description="Login path")
     * )
     */

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

                $this->session->set("token",$data['token']);
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
                "message" => "Login unsuccessful."
            );
            if ($remaining) {
                $data['messages'][] = array(
                    "type" => "warning",
                    "message" => "{$remaining} Attempts remaining"
                );
            }

            $data['active'] = true;
        }
        // login failed

        return $this->responder->withJson($response, $data);


    }


}

