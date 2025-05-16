<?php

namespace App\Resolvers\Registration;

use Kwidoo\Mere\Contracts\Repositories\OrganizationRepository;
use Kwidoo\Mere\Contracts\Repositories\SystemSettingRepository;
use App\Contracts\Resolvers\OrganizationResolver;
use App\Data\Registration\RegistrationConfigData;
use App\Enums\RegistrationIdentity;
use App\Enums\RegistrationProfile;
use App\Enums\RegistrationSecret;
use App\Resolvers\Organizations\CreateOrganizationConfigResolver;
use Kwidoo\Mere\Contracts\Models\OrganizationInterface;
use Kwidoo\Mere\Contracts\Models\UserInterface;

class RegistrationConfigResolver
{
    /**
     * @var \App\Models\Organization|null
     */
    protected ?OrganizationInterface $organization = null;

    /**
     * @var \App\Models\User|null
     */
    protected ?UserInterface $user = null;

    /**
     * Initialize the resolver with required repositories and resolvers.
     *
     * @param OrganizationRepository $repository Organization repository
     * @param SystemSettingRepository $settingRepository System settings repository
     * @param OrganizationResolver $resolver Organization resolver
     */
    public function __construct(
        protected SystemSettingRepository $settingRepository,
        protected CreateOrganizationConfigResolver $organizationConfig
    ) {}

    /**
     * Set the context organization by ID or model.
     *
     * @param \App\Models\Organization|string|null $organization Organization to set
     *
     * @return self
     */
    public function forOrganization(OrganizationInterface|string|null $organization): self
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Optionally set the user context.
     *
     * @param \App\Models\User|null $user User to set
     *
     * @return self
     */
    public function forUser(?UserInterface $user): self
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
        $orgConfig = $this->organizationConfig
            ->forOrganization($this->organization)
            ->forUser($this->user)
            ->organizationCreationConfig();

        return new RegistrationConfigData(
            flow: $orgConfig->flow,
            mode: $orgConfig->mode,
            identity: $this->determineIdentity(),
            profile: $this->determineProfile(),
            secret: $this->determineSecret(),
        );
    }

    /**
     * Get the current organization from context.
     *
     * @return  \App\Models\Organization|null
     */
    public function getOrganization(): ?OrganizationInterface
    {
        return $this->organization;
    }



    // Flow and mode determination is now handled by CreateOrganizationConfigResolver


    /**
     * Determine the registration identity for the current context.
     * Currently defaults to email-based registration.
     *
     * @return RegistrationIdentity
     */
    protected function determineIdentity(): RegistrationIdentity
    {
        return RegistrationIdentity::EMAIL;
    }

    /**
     * Determine the registration profile for the current context.
     * Currently defaults to standard profile creation.
     *
     * @return RegistrationProfile
     */
    protected function determineProfile(): RegistrationProfile
    {
        return RegistrationProfile::DEFAULT_PROFILE;
    }

    /**
     * Determine the registration secret for the current context.
     * Currently defaults to password-based authentication.
     *
     * @return RegistrationSecret
     */
    protected function determineSecret(): RegistrationSecret
    {
        return RegistrationSecret::PASSWORD;
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
}
