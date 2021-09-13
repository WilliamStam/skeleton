<?php

namespace System\Core;

use System\Sessions\SessionHandlerInterface;
use System\Utilities\Arrays;

class Session {
    private $data = array();
    private SessionHandlerInterface $handler;

    function __construct(SessionHandlerInterface $handler, Profiler $profiler) {
        $this->handler = $handler;
        $this->profiler = $profiler;
    }

    public function start(): Session {


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
        if (!session_start()) {
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

    public function regenerateId(): void {
        if (!$this->isStarted()) {
            throw new SessionException('Cannot regenerate the session ID for non-active sessions.');
        }

        if (headers_sent()) {
            throw new SessionException('Headers have already been sent.');
        }

        if (!session_regenerate_id(true)) {
            throw new SessionException('The session ID could not be regenerated.');
        }
        $this->clear();

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                $this->getName(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        if (session_unset() === false) {
            throw new SessionException('The session could not be unset.');
        }

        if (session_destroy() === false) {
            throw new SessionException('The session could not be destroyed.');
        }

    }

    public function destroy(): void {
        if (!$this->isStarted()) {
            return;
        }

        $this->data = array();
        $this->regenerateId();
    }

    public function clear(): void {
        $this->data = array();

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

    /**
     * {@inheritdoc}
     */
    public function setName(string $name): void {
        if ($this->isStarted()) {
            throw new SessionException('Cannot change session name when session is active');
        }
        session_name($name);
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

final class SessionException extends \RuntimeException {
}