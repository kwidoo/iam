<?php

namespace App\Providers;

use App\Exceptions\AuthGuardSetupException;
use App\Guards\IamGuard;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

// Contracts
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

use App\Contracts\Repositories\{
    RoleRepository,
    PermissionRepository,
    EventSourcingRepository,
    InvitationRepository,
    MicroserviceRepository,
    OrganizationRepository,
    ProfileRepository,
    UserRepository,
};
use App\Models\Profile;
use App\Factories\{

    StrategySelectorFactory,
};

use App\Strategies\{
    Identity\EmailIdentityStrategy,
    Identity\PhoneIdentityStrategy,
    Password\WithOTP,
    Password\WithPassword,
    Organization\MainOnlyStrategy,
    Organization\UserCreatesOrgStrategy,
    Profile\ProfileStrategy,
};

// Implementations
use App\Services\{
    OrganizationService,
    RoleService,
    PermissionService,
    EventSourcingService,
    MicroserviceService,
    ProfileService,
    RegistrationService,
    UserService,
};

use App\Repositories\{
    RoleRepositoryEloquent,
    PermissionRepositoryEloquent,
    EventSourcingRepositoryEloquent,
    InvitationRepositoryEloquent,
    MicroserviceRepositoryEloquent,
    OrganizationRepositoryEloquent,
    ProfileRepositoryEloquent,
    UserRepositoryEloquent,
};


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Passport::ignoreRoutes();

        $this->registerServices();
        $this->registerRepositories();
    }

    protected function registerServices(): void
    {
        $this->app->bind(OrganizationServiceContract::class, OrganizationService::class);
        $this->app->bind(RoleServiceContract::class, RoleService::class);
        $this->app->bind(PermissionServiceContract::class, PermissionService::class);
        $this->app->bind(EventSourcingServiceContract::class, EventSourcingService::class);
        $this->app->bind(MicroserviceServiceContract::class, MicroserviceService::class);
        $this->app->bind(RegistrationServiceContract::class, RegistrationService::class);
        $this->app->bind(ProfileServiceContract::class, ProfileService::class);
        $this->app->bind(UserServiceContract::class, UserService::class);

        $this->app->singleton(StrategySelectorFactory::class, function () {
            return new StrategySelectorFactory([
                'email' => EmailIdentityStrategy::class,
                'phone' => PhoneIdentityStrategy::class,
                'user_creates_org' => UserCreatesOrgStrategy::class,
                'main_only' => MainOnlyStrategy::class,
                'default_profile' => ProfileStrategy::class,
                'otp' => WithOTP::class,
                'password' => WithPassword::class,
            ]);
        });
    }

    protected function registerRepositories(): void
    {
        $this->app->bind(RoleRepository::class, RoleRepositoryEloquent::class);
        $this->app->bind(PermissionRepository::class, PermissionRepositoryEloquent::class);
        $this->app->bind(EventSourcingRepository::class, EventSourcingRepositoryEloquent::class);
        $this->app->bind(MicroserviceRepository::class, MicroserviceRepositoryEloquent::class);
        $this->app->bind(OrganizationRepository::class, OrganizationRepositoryEloquent::class);
        $this->app->bind(UserRepository::class, UserRepositoryEloquent::class);
        $this->app->bind(InvitationRepository::class, InvitationRepositoryEloquent::class);
        $this->app->bind(ProfileRepository::class, ProfileRepositoryEloquent::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'user' => User::class,
            'profile' => Profile::class,
        ]);

        Auth::extend('iam', function ($app, $name, array $config) {
            if (!isset($config['provider'])) {
                throw new AuthGuardSetupException("The iam guard requires a 'provider' config key");
            }

            $provider = $config['provider'];

            return new IamGuard(
                Auth::createUserProvider($provider),
                $app->make('request')
            );
        });
    }
}
