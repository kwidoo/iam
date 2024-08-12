<?php

namespace App\Providers;

use App\Contracts\AclService as AclServiceContract;

use App\Contracts\Services\CreateUserService;
use App\Exceptions\AuthGuardSetupException;
use App\Guards\IamGuard;
use App\Models\Organization;
use App\Models\Profile;
use App\Models\User;
use App\Services\AclService;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Passport::ignoreRoutes();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'user' => User::class,
            'organization' => Organization::class,
            'profile' => Profile::class,

        ]);

        Auth::extend('iam', function ($app, $name, array $config) {
            if (!array_key_exists('provider', $config)) {
                throw new AuthGuardSetupException("The iam guard requires a 'provider' config key");
            }
            /** @var string|null */
            $provider = $config['provider'];
            return new IamGuard(Auth::createUserProvider($provider), $app->make('request'));
        });

        Passport::enablePasswordGrant();

        $userField = ucfirst(config('iam.user_field', 'email'));
        $class = "App\\Services\\Create" . $userField . "UserService";

        $this->app->bind(
            CreateUserService::class,
            $class
        );



        //  $this->app->bind(MicroServiceAggregateContract::class, MicroServiceAggregate::class);
        // $this->app->bind(CreateMicroServiceContract::class, CreateMicroService::class);

        $this->app->bind(AclServiceContract::class, AclService::class);
    }
}
