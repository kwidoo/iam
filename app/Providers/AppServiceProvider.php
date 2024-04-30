<?php

namespace App\Providers;

use App\Aggregates\UserAggregate;
use App\Contracts\CreateUser;
use App\Contracts\CreateUserService;
use App\Contracts\LoginUser;
use App\Contracts\UserAggregate as UserAggregateContract;
use App\Services\CreateRootUserService;
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

        Passport::enablePasswordGrant();

        $this->app->bind(
            CreateUserService::class,
            CreateRootUserService::class
        );

        $this->app->bind(UserAggregateContract::class, UserAggregate::class);
        $this->app->bind(CreateUser::class, UserAggregate::class);
        $this->app->bind(LoginUser::class, UserAggregate::class);
    }
}
