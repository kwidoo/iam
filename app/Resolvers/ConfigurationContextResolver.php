<?php

namespace App\Resolvers;

use App\Models\Organization;
use App\Models\User;
use App\Contracts\Repositories\OrganizationRepository;
use App\Contracts\Repositories\SystemSettingRepository;
use App\Contracts\Resolvers\OrganizationResolver;
use App\Data\RegistrationConfigData;
use App\Enums\RegistrationMode;
use App\Enums\RegistrationFlow;
use App\Enums\RegistrationIdentity;
use App\Enums\RegistrationProfile;
use App\Enums\RegistrationSecret;

/**
 * Resolver for registration configuration context.
 * Determines appropriate registration settings based on organization and user context.
 *
 * @category App\Resolvers
 * @package  App\Resolvers
 * @author   John Doe <john.doe@example.com>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/your-repo
 */
class ConfigurationContextResolver
{
    protected ?Organization $organization = null;
    protected ?User $user = null;

    /**
     * Initialize the resolver with required repositories and resolvers.
     *
     * @param OrganizationRepository  $repository        Organization repository
     * @param SystemSettingRepository $settingRepository System settings repository
     * @param OrganizationResolver    $resolver          Organization resolver
     */
    public function __construct(
        protected OrganizationRepository $repository,
        protected SystemSettingRepository $settingRepository,
        protected OrganizationResolver $resolver
    ) {
    }

    /**
     * Set the context organization by ID or model.
     *
     * @param string|Organization|null $organization Organization to set
     *
     * @return self
     */
    public function forOrg(string|Organization|null $organization): self
    {
        if (is_string($organization)) {
            $this->organization = $this->resolver->resolve($organization);
        } elseif ($organization instanceof Organization) {
            $this->organization = $organization;
        }

        return $this;
    }

    /**
     * Optionally set the user context.
     *
     * @param User|null $user User to set
     *
     * @return self
     */
    public function forUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get full registration config from current context.
     * Determines all registration settings based on current organization and user.
     *
     * @return RegistrationConfigData
     */
    public function registrationConfig(): RegistrationConfigData
    {
        return new RegistrationConfigData(
            flow: $this->determineFlow(),
            mode: $this->determineMode(),
            identity: $this->determineIdentity(),
            profile: $this->determineProfile(),
            secret: $this->determineSecret(),
        );
    }

    /**
     * Determine the current registration flow.
     * Based on organization existence and user-organization relationship.
     *
     * @return RegistrationFlow
     */
    public function determineFlow(): RegistrationFlow
    {
        if ($this->repository->all()->isEmpty()) {
            return RegistrationFlow::INITIAL_BOOTSTRAP;
        }
        if ($this->organization && !$this->organization->owner->is($this->user)) {
            return RegistrationFlow::USER_JOINS_USER_ORG;
        }

        return config('iam.defaults.registration_strategy', RegistrationFlow::MAIN_ONLY);
    }

    /**
     * Determine the registration mode for the current org.
     * Checks if organization requires invite codes for registration.
     *
     * @return RegistrationMode
     */
    public function determineMode(): RegistrationMode
    {
        return $this->organization?->registration_mode ?? RegistrationMode::INVITE_ONLY;
    }

    /**
     * Get boolean setting value from repository or config.
     *
     * @param string $key       Setting key
     * @param string $configKey Config key fallback
     *
     * @return bool
     */
    protected function settingBool(string $key, string $configKey): bool
    {
        $setting = $this->settingRepository->findByField('key', $key)->first();

        return $setting !== null
            ? filter_var($setting->value, FILTER_VALIDATE_BOOLEAN)
            : config($configKey, false);
    }

    /**
     * Determine the registration identity for the current context.
     * Currently defaults to email-based registration.
     *
     * @return RegistrationIdentity
     */
    public function determineIdentity(): RegistrationIdentity
    {
        return RegistrationIdentity::EMAIL;
    }

    /**
     * Determine the registration profile for the current context.
     * Currently defaults to standard profile creation.
     *
     * @return RegistrationProfile
     */
    public function determineProfile(): RegistrationProfile
    {
        return RegistrationProfile::DEFAULT_PROFILE;
    }

    /**
     * Determine the registration secret for the current context.
     * Currently defaults to password-based authentication.
     *
     * @return RegistrationSecret
     */
    public function determineSecret(): RegistrationSecret
    {
        return RegistrationSecret::PASSWORD;
    }

    /**
     * Get the current organization from context.
     *
     * @return Organization|null
     */
    public function getOrg(): ?Organization
    {
        return $this->organization;
    }
}
