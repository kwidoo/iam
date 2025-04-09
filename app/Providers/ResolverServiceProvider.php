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

/**
 * Service provider for registration strategy resolvers.
 * Registers and configures strategy resolvers for different registration aspects.
 *
 * @category App\Providers
 * @package  App\Providers
 * @author   John Doe <john.doe@example.com>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/your-repo
 */
class ResolverServiceProvider extends ServiceProvider
{
    /**
     * Register services in the container.
     * Sets up strategy resolvers for identity, flow, profile, and secret handling.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(
            RegistrationStrategyResolver::class, function () {
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
            }
        );

        $this->app->runningInConsole()
            ? $this->app->bind(OrganizationResolver::class, ConsoleOrganizationResolver::class)
            : $this->app->bind(OrganizationResolver::class, HttpOrganizationResolver::class);
    }
}
