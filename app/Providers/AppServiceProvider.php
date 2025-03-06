<?php

namespace App\Providers;

use App\Contracts\Services\CreateUserService;
use App\Exceptions\AuthGuardSetupException;
use App\Guards\IamGuard;
use App\Models\User;
use App\Services\UserRepository;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Laravel\Passport\Bridge\UserRepository as PassportUserRepository;

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

        Passport::enablePasswordGrant();
    }
}
