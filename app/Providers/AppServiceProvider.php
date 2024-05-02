<?php

namespace App\Providers;

use App\Aggregates\UserAggregate;
use App\Contracts\CreateUser;
use App\Contracts\CreateUserService;
use App\Contracts\LoginUser;
use App\Contracts\UserAggregate as UserAggregateContract;
use App\Guards\IamGuard;
use App\Models\User;
use App\Services\CreateRootUserService;
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
            return new IamGuard(Auth::createUserProvider($config['provider']), $app->make('request'));
        });

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
