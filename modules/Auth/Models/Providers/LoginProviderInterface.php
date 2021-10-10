<?php

namespace Modules\Auth\Models\Providers;

use App\Models\SystemUsers;

interface LoginProviderInterface {

    public function __invoke(string $username, string $password): ?SystemUsers;
}