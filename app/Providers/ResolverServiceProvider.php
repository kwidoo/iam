<?php

namespace App\Providers;

use App\Contracts\Resolvers\OrganizationResolver;
use App\Contracts\Services\ProfileService;
use Illuminate\Support\ServiceProvider;

use App\Enums\{
    RegistrationIdentity,
    OrganizationFlow,
    OrganizationMode,
    RegistrationProfile,
    RegistrationSecret,
};
use App\Resolvers\Organizations\CreateOrganizationStrategyResolver;
use App\Resolvers\Organizations\HttpOrganizationResolver;
use App\Resolvers\Organizations\ConsoleOrganizationResolver;
use App\Resolvers\Registration\RegistrationStrategyResolver;
use App\Services\Organizations\InitialBootstrap;
use App\Services\Organizations\MainOnly;
use App\Services\Organizations\UserCreatesOrg;
use App\Services\Organizations\UserJoinsUserOrg;
use App\Services\Registration\Identity\EmailIdentity;
use App\Services\Registration\Identity\PhoneIdentity;
use App\Services\Registration\Password\WithOTP;
use App\Services\Registration\Password\WithPassword;

class ResolverServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // possible registration strategies:
        $this->app->singleton(
            RegistrationStrategyResolver::class,
            function ($app) {
                return new RegistrationStrategyResolver(
                    container: $app,
                    identityServices: [
                        RegistrationIdentity::EMAIL->value => EmailIdentity::class,
                        RegistrationIdentity::PHONE->value => PhoneIdentity::class,
                    ],
                    flowServices: [
                        OrganizationFlow::MAIN_ONLY->value => MainOnly::class,
                        OrganizationFlow::USER_CREATES_ORG->value => UserCreatesOrg::class,
                        OrganizationFlow::INITIAL_BOOTSTRAP->value => InitialBootstrap::class,
                        OrganizationFlow::USER_JOINS_USER_ORG->value => UserJoinsUserOrg::class,
                    ],
                    profileServices: [
                        RegistrationProfile::DEFAULT_PROFILE->value => ProfileService::class,
                    ],
                    secretServices: [
                        RegistrationSecret::PASSWORD->value => WithPassword::class,
                        RegistrationSecret::OTP->value => WithOTP::class,
                    ],
                    modeServices: []
                );
            }
        );

        $this->app->singleton(CreateOrganizationStrategyResolver::class, function ($app) {
            return new CreateOrganizationStrategyResolver(
                container: $app,
                flowServices: [
                    OrganizationFlow::MAIN_ONLY->value => MainOnly::class,
                    OrganizationFlow::USER_CREATES_ORG->value => UserCreatesOrg::class,
                    OrganizationFlow::INITIAL_BOOTSTRAP->value => InitialBootstrap::class,
                    OrganizationFlow::USER_JOINS_USER_ORG->value => UserJoinsUserOrg::class,
                ],
                modeServices: [
                    //   OrganizationMode::INVITE_ONLY->value => MainOnly::class,
                    // OrganizationMode::OPEN->value => UserCreat::class,
                ]
            );
        });

        $this->app->runningInConsole()
            ? $this->app->bind(OrganizationResolver::class, ConsoleOrganizationResolver::class)
            : $this->app->bind(OrganizationResolver::class, HttpOrganizationResolver::class);
    }
}
