<?php

namespace App\Factories;

use App\Contracts\Repositories\OrganizationRepository;
use App\Enums\RegistrationMode;
use App\Enums\RegistrationFlow;
use App\Models\Organization;

class ConfigurationContext
{
    protected ?Organization $organization = null;

    public function __construct(protected OrganizationRepository $repository) {}

    public function forOrg(?string $orgId): self
    {
        if ($orgId) {
            $this->organization = $this->repository->find($orgId);
        }
        // You could look up OrgAuthRules or SystemSetting here
        return $this;
    }

    public function strategy(): RegistrationFlow
    {
        if ($this->repository->all()->isEmpty()) {
            return RegistrationFlow::INITIAL_BOOTSTRAP;
        }
        // Stubbed fallback for now
        return config('iam.defaults.registration_strategy', RegistrationFlow::MAIN_ONLY);
    }

    public function registrationMode(): RegistrationMode
    {
        return $this->organization?->registration_mode ?? RegistrationMode::INVITE_ONLY;
    }
}
