<?php

namespace App\Providers;

use App\Contracts\UserResolver as UserResolverContract;
use App\Resolvers\ResolveByEmail;
use App\Resolvers\ResolveByPhone;
use App\Resolvers\UserResolver;
use App\Services\UserRepository;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Bridge\UserRepository as PassportUserRepository;

class UserProviders extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(UserResolverContract::class, function () {
            return new UserResolver([
                'email' => ResolveByEmail::class,
                'phone' => ResolveByPhone::class,
                // Add more resolvers here
            ]);
        });

        $this->app->bind(PassportUserRepository::class, UserRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
