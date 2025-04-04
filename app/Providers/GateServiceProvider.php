<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class GateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }
    public function boot(): void
    {
        Gate::define('register-users', function ($user, $organization) {
            return $user->hasRole("{$organization->slug}-admin") || $user->hasRole('super-admin');
        });
    }
}
