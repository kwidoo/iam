<?php

namespace App\Providers;

use App\Adapters\LifecycleBridge;
use App\Aggregates\RegistrationAggregate;
use App\Services\ContactPasswordChecker;
use Illuminate\Support\ServiceProvider;
use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle as NewLifecycle;
use Kwidoo\Lifecycle\Lifecycle\DefaultLifecycle;
use Kwidoo\Mere\Contracts\Aggregate;
use Kwidoo\Mere\Contracts\Lifecycle as OldLifecycle;
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

        // Bind the new Lifecycle interface to its implementation
        $this->app->bind(NewLifecycle::class, DefaultLifecycle::class);

        // Bind the old Lifecycle interface to our bridge adapter
        // This allows all existing code to continue working while delegating to the new implementation
        $this->app->bind(OldLifecycle::class, function ($app) {
            return new LifecycleBridge(
                $app->make(NewLifecycle::class)
            );
        });
    }

    public function boot(): void
    {
        // Reserved for future use.
    }
}
