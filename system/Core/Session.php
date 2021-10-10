<?php

namespace System\Core;

use System\Sessions\SessionHandlerInterface;
use System\Utilities\Arrays;

class Session {
    private $data = array();
    private SessionHandlerInterface $handler;
    private $options = array();

    function __construct(SessionHandlerInterface $handler, Profiler $profiler) {
        $this->handler = $handler;
        $this->profiler = $profiler;
    }


    public function start($options=null): Session {

        if (is_array($options)){
            $this->options = $options;
        }

        if ($this->isStarted()) {
            throw new SessionException('Failed to start the session: Already started.');
        }

        if (headers_sent($file, $line) && filter_var(ini_get('session.use_cookies'), FILTER_VALIDATE_BOOLEAN)) {
            throw new SessionException(
                sprintf(
                    'Failed to start the session because headers have already been sent by "%s" at line %d.',
                    $file,
                    $line
                )
            );
        }

        // Try and start the session
        if (!session_start($this->options)) {
            throw new SessionException('Failed to start the session.');
        }




        $this->data = Arrays::merge((array)$this->data,(array)json_decode($this->handler->read($this->getId()), true));


        if (!$this->has("CSRF_TOKEN")){
            $this->set("CSRF_TOKEN","csrf_tokens");
        }

        return $this;
    }

    public function isStarted(): bool {
       return session_status() === PHP_SESSION_ACTIVE;
    }


    public function destroy(): void {
        if (!$this->isStarted()) {
            return;
        }

        if (session_unset() === false) {
            throw new SessionException('The session could not be unset.');
        }

        if (session_destroy() === false) {
            throw new SessionException('The session could not be destroyed.');
        }



        $this->start();
    }

    public function clear(): void {
        $this->data = array();

    }

    public function id(): string {
        return $this->getId();
    }
    public function getId(): string {
        return (string)session_id();
    }

    public function setId(string $id): void {
        if ($this->isStarted()) {
            throw new SessionException('Cannot change session id when session is active');
        }

        session_id($id);
    }

    public function getName(): string {
        return (string)session_name();
    }


    public function setName(string $name): void {
        if ($this->isStarted()) {
            throw new SessionException('Cannot change session name when session is active');
        }
        session_name($name);
    }
    public function setOptions(array $options) : Session {
        $this->options = $options;
        return $this;
    }

    public function has(string $key): bool {
        if (empty($this->data)) {
            return false;
        }

        return array_key_exists($key, $this->data);
    }

    public function save() {

        $this->handler->write($this->getId(), json_encode((array)$this->data, JSON_PRETTY_PRINT));
    }

    public function __get($key) {
        return  $this->get($key) ;
    }

    public function get(string $key, $default = null) {
        return $this->has($key) ? $this->data[$key] : $default;
    }

    public function set(string $key, $value = null) {
        $this->data[$key] = $value;
    }

    public function toArray() : array {
        return $this->data;
    }


}

class SessionException extends \RuntimeException {}