<?php

namespace App\Providers;

use App\Exceptions\AuthGuardSetupException;
use App\Guards\IamGuard;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Passport::ignoreRoutes();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'user' => User::class,
        ]);

        Auth::extend('iam', function ($app, $name, array $config) {
            if (!array_key_exists('provider', $config)) {
                throw new AuthGuardSetupException("The iam guard requires a 'provider' config key");
            }
            /** @var string|null */
            $provider = $config['provider'];
            return new IamGuard(Auth::createUserProvider($provider), $app->make('request'));
        });
    }
}
