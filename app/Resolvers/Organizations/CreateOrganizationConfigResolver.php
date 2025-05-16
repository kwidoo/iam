<?php

namespace App\Resolvers\Organizations;

use Kwidoo\Mere\Contracts\Repositories\OrganizationRepository;
use Kwidoo\Mere\Contracts\Repositories\SystemSettingRepository;
use App\Contracts\Resolvers\OrganizationResolver;
use App\Data\Organizations\OrganizationConfigData;
use App\Enums\OrganizationMode;
use App\Enums\OrganizationFlow;
use Kwidoo\Mere\Contracts\Models\OrganizationInterface;
use Kwidoo\Mere\Contracts\Models\UserInterface;

class CreateOrganizationConfigResolver
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
        protected OrganizationRepository $repository,
        protected SystemSettingRepository $settingRepository,
        protected OrganizationResolver $resolver
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

        if (is_string($organization)) {
            $this->organization = $this->resolver->resolve($organization);
        }

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
     * Get organization creation flow configuration from current context.
     * Determines appropriate organization creation flow based on current organization and user.
     * Only includes flow and mode which are relevant for organization creation.
     *
     * @return OrganizationConfigData
     */
    public function organizationCreationConfig(): OrganizationConfigData
    {
        return new OrganizationConfigData(
            flow: $this->determineFlow(),
            mode: $this->determineMode(),
        );
    }

    /**
     * Get the current organization from context.
     *
     * @return \App\Models\Organization|null
     */
    public function getOrganization(): ?OrganizationInterface
    {
        return $this->organization;
    }

    /**
     * Determine the current organization creation flow.
     * Based on organization existence and user-organization relationship.
     *
     * @return OrganizationFlow
     */
    protected function determineFlow(): OrganizationFlow
    {
        if ($this->repository->all()->isEmpty()) {
            return OrganizationFlow::INITIAL_BOOTSTRAP;
        }
        if ($this->organization && !$this->organization->owner->is($this->user)) {
            return OrganizationFlow::USER_JOINS_USER_ORG;
        }

        return config('iam.defaults.registration_strategy', OrganizationFlow::MAIN_ONLY);
    }

    /**
     * Determine the registration mode for the current org.
     * Checks if organization requires invite codes for registration.
     *
     * @return OrganizationMode
     */
    protected function determineMode(): OrganizationMode
    {
        return $this->organization?->registration_mode ?? OrganizationMode::INVITE_ONLY;
    }

    // Only flow and mode are needed for organization creation

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
