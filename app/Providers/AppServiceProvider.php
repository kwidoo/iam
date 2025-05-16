<?php

namespace App\Providers;

use App\Adapters\LifecycleBridge;
use App\Aggregates\RegistrationAggregate;
use App\Models\Organization;
use App\Models\User;
use App\Observers\OrganizationObserver;
use App\Services\ContactPasswordChecker;
use Illuminate\Support\ServiceProvider;
use Kwidoo\Contacts\Contracts\Contactable;
use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;
use Kwidoo\Lifecycle\Core\Engine\DefaultLifecycle;
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
        $this->app->bind(Lifecycle::class, DefaultLifecycle::class);
        $this->app->bind(Contactable::class, User::class);
    }

    public function boot(): void
    {
        Organization::observe(OrganizationObserver::class);
    }
}
