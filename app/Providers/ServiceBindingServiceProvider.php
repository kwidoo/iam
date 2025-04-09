<?php

namespace App\Providers;

use App\Aggregates\RegistrationAggregate;
use Illuminate\Support\ServiceProvider;
use App\Contracts\Services\{
    OrganizationService as OrganizationServiceContract,
    RoleService as RoleServiceContract,
    PermissionService as PermissionServiceContract,
    EventSourcingService as EventSourcingServiceContract,
    MicroserviceService as MicroserviceServiceContract,
    RegistrationService as RegistrationServiceContract,
    ProfileService as ProfileServiceContract,
    UserService as UserServiceContract,
};
use App\Factories\AccessAssignmentFactory;
use App\Services\{
    OrganizationService,
    RoleService,
    PermissionService,
    EventSourcingService,
    MicroserviceService,
    RegistrationService,
    ProfileService,
    UserService,
};
use Kwidoo\Mere\Contracts\Aggregate;

class ServiceBindingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(OrganizationServiceContract::class, OrganizationService::class);
        $this->app->bind(RoleServiceContract::class, RoleService::class);
        $this->app->bind(PermissionServiceContract::class, PermissionService::class);
        $this->app->bind(EventSourcingServiceContract::class, EventSourcingService::class);
        $this->app->bind(MicroserviceServiceContract::class, MicroserviceService::class);
        $this->app->bind(RegistrationServiceContract::class, RegistrationService::class);
        $this->app->bind(ProfileServiceContract::class, ProfileService::class);
        $this->app->bind(UserServiceContract::class, UserService::class);

        $this->app->bind(Aggregate::class, RegistrationAggregate::class);
    }
}
