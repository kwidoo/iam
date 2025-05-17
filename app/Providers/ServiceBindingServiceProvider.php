<?php

namespace App\Providers;

use App\Contracts\Services\Organizations\ConnectProfileService;
use App\Contracts\Services\Organizations\ConnectUserService;
use App\Contracts\Services\Organizations\OrganizationAccessService;
use App\Contracts\Services\Organizations\OrganizationService;
use App\Contracts\Services\Organizations\RoleSetupService;
use App\Contracts\Services\PermissionService;
use App\Contracts\Services\ProfileService;
use App\Contracts\Services\Registration\RegistrationService;
use App\Contracts\Services\Roles\RoleAssignment;
use App\Contracts\Services\Roles\RoleService;
use App\Services\DefaultConnectProfileService;
use App\Services\DefaultProfileService;
use App\Services\DefaultRegistrationService;
use App\Services\Organizations\DefaultConnectUserServiceService;
use App\Services\Organizations\DefaultOrganizationService;
use App\Services\Organizations\DefaultOrganizationAccessService;
use App\Services\Organizations\DefaultRoleSetupService;
use App\Services\Permissions\DefaultPermissionService;
use App\Services\Permissions\PermissionAssignment;
use App\Services\Permissions\StandardUserPermissionAssignment;
use App\Services\Roles\DefaultRoleService;
use App\Services\Roles\StandardUserRoleAssignment;
use Illuminate\Support\ServiceProvider;
use PhpParser\PrettyPrinter\Standard;

class ServiceBindingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind the new Lifecycle interface to its implementation
        // $this->app->bind(Lifecycle::class, DefaultLifecycle::class);

        // Bind service contracts to their implementations
        $this->app->bind(OrganizationService::class, DefaultOrganizationService::class);
        $this->app->bind(RoleSetupService::class, DefaultRoleSetupService::class);
        // $this->app->bind(OrganizationAccessService::class, DefaultOrganizationAccessService::class);
        $this->app->bind(RoleService::class, DefaultRoleService::class);
        $this->app->bind(PermissionService::class, DefaultPermissionService::class);
        $this->app->bind(RegistrationService::class, DefaultRegistrationService::class);
        $this->app->bind(OrganizationAccessService::class, DefaultOrganizationAccessService::class);
        $this->app->bind(ConnectUserService::class, DefaultConnectUserServiceService::class);
        $this->app->bind(ConnectProfileService::class, DefaultConnectProfileService::class);

        $this->app->bind(RoleAssignment::class, StandardUserRoleAssignment::class);
        $this->app->bind(PermissionAssignment::class, StandardUserPermissionAssignment::class);


        // $this->app->bind(EventSourcingServiceContract::class, EventSourcingService::class);
        // $this->app->bind(MicroserviceServiceContract::class, MicroserviceService::class);
        // $this->app->bind(RegistrationServiceContract::class, RegistrationService::class);
        $this->app->bind(ProfileService::class, DefaultProfileService::class);
        // $this->app->bind(UserServiceContract::class, UserService::class);

        // $this->app->bind(Aggregate::class, RegistrationAggregate::class);
    }
}
