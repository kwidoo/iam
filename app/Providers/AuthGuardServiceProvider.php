<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use App\Guards\IamGuard;
use App\Exceptions\AuthGuardSetupException;

class AuthGuardServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Auth::extend('iam', function ($app, $name, array $config) {
            if (!isset($config['provider'])) {
                throw new AuthGuardSetupException("The iam guard requires a 'provider' config key");
            }

            $provider = $config['provider'];

            return new IamGuard(
                Auth::createUserProvider($provider),
                $app->make('request')
            );
        });
    }
}
