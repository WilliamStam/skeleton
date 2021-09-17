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

    public function __invoke(Request $request, Response $response): Response {

        $this->LoginRepository->setSession($request->getAttribute("SESSION")->id());

        $data = array();
        $data['messages'] = array();

        $user = null;


        // if login attempts in less than set value then we good to try again
        if ($this->LoginRepository->attempts($this->system->get("SETTINGS.auth.minutes")) < $this->system->get("SETTINGS.auth.attempts")) {
            $user = $this->LoginRepository->login($this->system->get("POST.username"), $this->system->get("POST.password"));

        } else {
            $data['messages'][] = array(
                "error",
                "Too Many attempts"
            );
            $data['attempts'] = $this->LoginRepository->attempts($this->system->get("SETTINGS.auth.minutes"));
            return $this->responder->withJson($response, $data)->withStatus(429, "Too many attempts");
        }


        // user logged in
        if ($user && $user->id()) {
            $data['messages'][] = array(
                "success",
                "Login successful"
            );

            $data['token'] = $this->LoginRepository->generateAndSaveToken($user);
            $data['user'] = $user->toSchema($this->userSchema);
//            $data['user'] = $user->toSchema()

            return $this->responder->withJson($response, $data);
        }

        // login failed
        $data['messages'][] = array(
            "error",
            "Login unsuccessful"
        );
        $data['attempts'] = $this->LoginRepository->attempts($this->system->get("SETTINGS.auth.minutes"));
        return $this->responder->withJson($response, $data)->withStatus(401, "Incorrect credentials");


    }


}

class TooManyLoginAttemptsException extends \Exception {
    protected $code = 500;
    protected $message = 'Error handler not found';
    public $headers = array();
    public $logLevel = LogLevel::EMERGENCY;
}