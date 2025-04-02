<?php

namespace App\Factories;

use App\Models\Organization;
use App\Models\User;
use App\Contracts\Repositories\OrganizationRepository;
use App\Contracts\Repositories\SystemSettingRepository;
use App\Data\RegistrationConfigData;
use App\Enums\RegistrationMode;
use App\Enums\RegistrationFlow;
use App\Enums\RegistrationIdentity;
use App\Enums\RegistrationProfile;
use App\Enums\RegistrationSecret;

class ConfigurationContext
{
    protected ?Organization $organization = null;
    protected ?User $user = null;

    public function __construct(
        protected OrganizationRepository $repository,
        protected SystemSettingRepository $systemSettingRepository
    ) {}

    /**
     * Set the context organization by ID or model.
     *
     * @param string|Organization|null $org
     * @return $this
     */
    public function forOrg(string|Organization|null $org): self
    {
        if (is_string($org)) {
            $this->organization = $this->repository->find($org);
        } elseif ($org instanceof Organization) {
            $this->organization = $org;
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
            identity: $this->user?->identity ?? RegistrationIdentity::EMAIL,
            profile: $this->user?->profile ?? RegistrationProfile::DEFAULT_PROFILE,
            secret: $this->user?->secret ?? RegistrationSecret::PASSWORD,
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

        if ($this->organization) {
            return $this->organization->registration_strategy
                ?? config('iam.defaults.registration_strategy', RegistrationFlow::MAIN_ONLY);
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

    /**
     * Global feature flag check from system settings.
     */
    public function featureEnabled(string $feature): bool
    {
        return (bool) $this->systemSettingRepository->get("feature.{$feature}", false);
    }
}
