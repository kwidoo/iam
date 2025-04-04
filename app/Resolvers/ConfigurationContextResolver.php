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

class ConfigurationContextResolver
{
    protected ?Organization $organization = null;
    protected ?User $user = null;

    public function __construct(
        protected OrganizationRepository $repository,
        protected SystemSettingRepository $settingRepository,
        protected OrganizationResolver $resolver
    ) {}

    /**
     * Set the context organization by ID or model.
     *
     * @param string|Organization|null $org
     * @return $this
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
     */
    public function forUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Full registration config from current context.
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
     */
    public function determineMode(): RegistrationMode
    {
        return $this->organization?->registration_mode ?? RegistrationMode::INVITE_ONLY;
    }

    protected function settingBool(string $key, string $configKey): bool
    {
        $setting = $this->settingRepository->findByField('key', $key)->first();

        return $setting !== null
            ? filter_var($setting->value, FILTER_VALIDATE_BOOLEAN)
            : config($configKey, false);
    }

    /**
     * Determine the registration identity for the current context.
     */
    public function determineIdentity(): RegistrationIdentity
    {
        return RegistrationIdentity::EMAIL;
    }

    /**
     * Determine the registration profile for the current context.
     */
    public function determineProfile(): RegistrationProfile
    {
        return RegistrationProfile::DEFAULT_PROFILE;
    }
    /**
     * Determine the registration secret for the current context.
     */
    public function determineSecret(): RegistrationSecret
    {
        return RegistrationSecret::PASSWORD;
    }

    public function getOrg(): ?Organization
    {
        return $this->organization;
    }
}
