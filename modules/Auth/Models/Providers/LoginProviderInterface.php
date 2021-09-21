<?php

namespace Modules\Auth\Models\Providers;

use App\Models\CurrentUserModel;

interface LoginProviderInterface {

    public function __invoke(string $username, string $password): ?CurrentUserModel;
}