<?php


namespace Modules\Auth\Controllers;

use App\DB;
use App\Responders\Responder;
use App\Schemas\CurrentUserSchema;
use Modules\Auth\LoginModel;
use App\Repositories\CurrentUserRepository;

use Modules\Auth\Repositories\LoginRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use System\Core\System;
use System\Core\Session;


class LoginController {

    function __construct(System $System, Responder $responder, LoginRepository $LoginRepository, Session $session, CurrentUserRepository $userRepository) {
        $this->system = $System;
        $this->session = $session;
        $this->responder = $responder;
        $this->LoginRepository = $LoginRepository;
        $this->userRepository = $userRepository;

    }


    /**
     * @OA\Get(
     *     path="/api/auth/login",
     *     @OA\Response(response="200", description="Fetches current login status")
     * )
     */
    public function get(Request $request, Response $response): Response {
        $errors = array();
        $messages = array();
        $data = array();
        $this->LoginRepository->setSession($request->getAttribute("SESSION"));
        $data['active'] = false;

        if ($request->getAttribute("USER") && $request->getAttribute("USER")->id) {
            $messages[] = array(
                "type" => "success",
                "message" => "You are already logged in."
            );
        } else {

            $attempts = $this->LoginRepository->attempts($this->system->get("SETTINGS.auth.minutes")) * 1;
            $remaining = ($this->system->get("SETTINGS.auth.attempts") * 1) - $attempts;

            if ($remaining <= 0) {
                $messages[] = array(
                    "type" => "error",
                    "message" => "Too many attempts. Try again later."
                );
            } elseif ($attempts) {
                $messages[] = array(
                    "type" => "warning",
                    "message" => "{$remaining} Attempts remaining"
                );
                $data['active'] = true;
            } else {
                $messages[] = array(
                    "type" => "info",
                    "message" => "Use your full email address and network password (same as you use to log into windows)."
                );
                $data['active'] = true;
            }

        }


        return $this->responder->withJson($response, array(
            "status" => count($errors) ? $this->responder::STATUS_FAILED : $this->responder::STATUS_SUCCESS,
            "data" => $data,
            "messages" => $messages,
            "errors" => $errors
        ));
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     @OA\Response(response="200", description="Login path")
     * )
     */

    public function post(Request $request, Response $response): Response {
        $errors = array();
        $messages = array();
        $data = array();



        $this->LoginRepository->setSession($request->getAttribute("SESSION"));

        $data['active'] = false;
        $data['username'] = $this->system->get("POST.username");

        $user = null;

        if (!$this->system->get("POST.username")){
            $errors['username'][] = "Username is required";
        }
        if (!$this->system->get("POST.password")){
            $errors['password'][] = "Password is required";
        }


        // if login attempts in less than set value then we good to try again
        if ( !count($errors)) {
            if ($this->LoginRepository->attempts($this->system->get("SETTINGS.auth.minutes")) < $this->system->get("SETTINGS.auth.attempts")) {
                $user = $this->LoginRepository->login($this->system->get("POST.username"), $this->system->get("POST.password"));


                if ($user && $user->id) {
                    $messages[] = array(
                        "type" => "success",
                        "message" => "Login successful"
                    );


                    $data['token'] = $this->LoginRepository->generateAndSaveToken($user);
                    $this->session->set("token", $data['token']);


                    $data['user'] = $user->toSchema(new CurrentUserSchema(
                        $this->userRepository->permissions($user)->toArray()
                     ));


                    return $this->responder->withJson($response, array(
                        "status" => count($errors) ? $this->responder::STATUS_FAILED : $this->responder::STATUS_SUCCESS,
                        "data" => $data,
                        "messages" => $messages,
                        "errors" => $errors
                    ));
                }
            }
        }

        $errors['username'][] = "Username or password not recognized";
        $errors['password'][] = "Username or password not recognized";

        // user logged in


        $attempts = $this->LoginRepository->attempts($this->system->get("SETTINGS.auth.minutes")) * 1;
        $remaining = ($this->system->get("SETTINGS.auth.attempts") * 1) - $attempts;

        if ($remaining == 0) {
            $messages[] = array(
                "type" => "error",
                "message" => "Too many attempts. Try again later."
            );
        } else {
           $messages[] = array(
                "type" => "error",
                "message" => "Login unsuccessful."
            );
            if ($remaining) {
                $messages[] = array(
                    "type" => "warning",
                    "message" => "{$remaining} Attempts remaining"
                );
            }

            $data['active'] = true;
        }
        // login failed

        return $this->responder->withJson($response, array(
            "status" => count($errors) ? $this->responder::STATUS_FAILED : $this->responder::STATUS_SUCCESS,
            "data" => $data,
            "messages" => $messages,
            "errors" => $errors
        ));


    }


}

