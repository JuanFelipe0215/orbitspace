<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Auth::provider('tenant_eloquent', function ($app, array $config) {
            return new \App\Auth\TenantUserProvider(
                $app['hash'],
                $config['model']
            );
        });
    }
}
