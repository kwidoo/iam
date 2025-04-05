<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\Resolvers\OrganizationResolver;
use App\Resolvers\{
    ConsoleOrganizationResolver,
    HttpOrganizationResolver,
    RegistrationStrategyResolver,
};
use App\Enums\{
    RegistrationIdentity,
    RegistrationFlow,
    RegistrationProfile,
    RegistrationSecret,
};
use App\Strategies\{
    Identity\EmailIdentityStrategy,
    Identity\PhoneIdentityStrategy,
    Password\WithOTP,
    Password\WithPassword,
    Organization\MainOnlyStrategy,
    Organization\UserCreatesOrgStrategy,
    Organization\InitialBootstrapStrategy,
    Organization\UserJoinsUserOrgStrategy,
    Profile\ProfileStrategy,
};

class ResolverServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(RegistrationStrategyResolver::class, function () {
            return new RegistrationStrategyResolver(
                identityStrategies: [
                    RegistrationIdentity::EMAIL->value => EmailIdentityStrategy::class,
                    RegistrationIdentity::PHONE->value => PhoneIdentityStrategy::class,
                ],
                flowStrategies: [
                    RegistrationFlow::MAIN_ONLY->value => MainOnlyStrategy::class,
                    RegistrationFlow::USER_CREATES_ORG->value => UserCreatesOrgStrategy::class,
                    RegistrationFlow::INITIAL_BOOTSTRAP->value => InitialBootstrapStrategy::class,
                    RegistrationFlow::USER_JOINS_USER_ORG->value => UserJoinsUserOrgStrategy::class,
                ],
                profileStrategies: [
                    RegistrationProfile::DEFAULT_PROFILE->value => ProfileStrategy::class,
                ],
                secretStrategies: [
                    RegistrationSecret::PASSWORD->value => WithPassword::class,
                    RegistrationSecret::OTP->value => WithOTP::class,
                ],
                modeStrategies: []
            );
        });

        $this->app->runningInConsole()
            ? $this->app->bind(OrganizationResolver::class, ConsoleOrganizationResolver::class)
            : $this->app->bind(OrganizationResolver::class, HttpOrganizationResolver::class);
    }
}
