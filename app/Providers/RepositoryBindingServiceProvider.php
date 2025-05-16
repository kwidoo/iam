<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\Repositories\{
    EventSourcingRepository,
    MicroserviceRepository,
    ProfileRepository,
};
use App\Data\DefaultAccessAssignmentData;
use App\Data\Registration\DefaultRegistrationData;
use App\Models\Invitation;
use App\Models\Organization;
use App\Models\User;
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
use Kwidoo\Mere\Contracts\Data\AccessAssignmentData;
use Kwidoo\Mere\Contracts\Data\RegistrationData;
use Kwidoo\Mere\Contracts\Models\InvitationInterface;
use Kwidoo\Mere\Contracts\Models\OrganizationInterface;
use Kwidoo\Mere\Contracts\Models\UserInterface;
use Kwidoo\Mere\Contracts\Repositories\InvitationRepository;
use Kwidoo\Mere\Contracts\Repositories\OrganizationRepository;
use Kwidoo\Mere\Contracts\Repositories\PermissionRepository;
use Kwidoo\Mere\Contracts\Repositories\RoleRepository;
use Kwidoo\Mere\Contracts\Repositories\SystemSettingRepository;
use Kwidoo\Mere\Contracts\Repositories\UserRepository;

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

        $this->app->bind(InvitationInterface::class, Invitation::class);
        $this->app->bind(UserInterface::class, User::class);
        $this->app->bind(OrganizationInterface::class, Organization::class);

        $this->app->bind(RegistrationData::class, DefaultRegistrationData::class);
        $this->app->bind(AccessAssignmentData::class, DefaultAccessAssignmentData::class);
    }
}
