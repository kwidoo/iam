<?php

namespace App\Providers;

use App\Aggregates\RegistrationAggregate;
use Illuminate\Support\ServiceProvider;
use Kwidoo\Mere\Contracts\Aggregate;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Passport::ignoreRoutes();
        $this->app->bind(
            Aggregate::class,
            RegistrationAggregate::class
        );
    }

    public function boot(): void
    {
        // Reserved for future use.
    }
}
