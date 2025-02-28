<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Aggregates\StandardUserAggregate;
use App\Contracts\Aggregates\UserAggregate;


class UserAggregateProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->bind(UserAggregate::class, StandardUserAggregate::class);
    }
}
