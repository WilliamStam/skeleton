<?php

namespace System\Sessions;

interface SessionHandlerInterface {
    /**
     * @param string $sessionId
     * @return mixed
     */
    public function read(string $sessionId);

    /**
     * @param string $sessionId
     * @param string $sessionData
     * @return void
     */
    public function write(string $sessionId, string $sessionData): bool;

    /**
     * Destroy a session
     *
     * @param string $sessionId The session ID being destroyed.
     * @return void
     */
    public function destroy($sessionId): bool;
}