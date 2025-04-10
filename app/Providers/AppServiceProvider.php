<?php

namespace App\Providers;

use App\Aggregates\RegistrationAggregate;
use App\Services\ContactPasswordChecker;
use Illuminate\Support\ServiceProvider;
use Kwidoo\Mere\Contracts\Aggregate;
use Kwidoo\MultiAuth\Contracts\PasswordCheckerInterface;
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

        $this->app->bind(PasswordCheckerInterface::class, ContactPasswordChecker::class);
    }

    public function boot(): void
    {
        // Reserved for future use.
    }
}
