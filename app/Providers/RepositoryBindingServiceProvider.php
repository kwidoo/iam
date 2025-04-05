<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\Repositories\{
    RoleRepository,
    PermissionRepository,
    EventSourcingRepository,
    InvitationRepository,
    MicroserviceRepository,
    OrganizationRepository,
    ProfileRepository,
    SystemSettingRepository,
    UserRepository,
};
use App\Repositories\{
    RoleRepositoryEloquent,
    PermissionRepositoryEloquent,
    EventSourcingRepositoryEloquent,
    InvitationRepositoryEloquent,
    MicroserviceRepositoryEloquent,
    OrganizationRepositoryEloquent,
    ProfileRepositoryEloquent,
    SystemSettingRepositoryEloquent,
    UserRepositoryEloquent,
};

class RepositoryBindingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RoleRepository::class, RoleRepositoryEloquent::class);
        $this->app->bind(PermissionRepository::class, PermissionRepositoryEloquent::class);
        $this->app->bind(EventSourcingRepository::class, EventSourcingRepositoryEloquent::class);
        $this->app->bind(MicroserviceRepository::class, MicroserviceRepositoryEloquent::class);
        $this->app->bind(OrganizationRepository::class, OrganizationRepositoryEloquent::class);
        $this->app->bind(UserRepository::class, UserRepositoryEloquent::class);
        $this->app->bind(InvitationRepository::class, InvitationRepositoryEloquent::class);
        $this->app->bind(ProfileRepository::class, ProfileRepositoryEloquent::class);
        $this->app->bind(SystemSettingRepository::class, SystemSettingRepositoryEloquent::class);
    }
}
