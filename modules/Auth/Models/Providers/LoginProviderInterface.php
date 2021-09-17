<?php

namespace Modules\Auth\Models\Providers;

use App\Models\UserCurrentModel;

interface LoginProviderInterface {

    public function __invoke(string $username, string $password): ?UserCurrentModel;
}