<?php

declare(strict_types=1);

namespace App\Tenancy\Bootstrappers;

use Illuminate\Auth\AuthManager;
use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;

class AuthTenancyBootstrapper implements TenancyBootstrapper
{
    public function __construct(protected AuthManager $auth) {}

    public function bootstrap(Tenant $tenant): void
    {
        $this->auth->forgetGuards();
    }

    public function revert(): void
    {
        $this->auth->forgetGuards();
    }
}
