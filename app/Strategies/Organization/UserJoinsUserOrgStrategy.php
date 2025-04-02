<?php

namespace App\Strategies\Organization;

use App\Contracts\Services\Strategy;
use App\Data\RegistrationData;
use App\Enums\RegistrationFlow;
use App\Enums\RegistrationMode;
use App\Services\OrganizationService;
use App\Services\InvitationService;
use Illuminate\Validation\ValidationException;

class UserJoinsUserOrgStrategy implements Strategy
{
    public function __construct(
        protected OrganizationService $organizationService,
        protected InvitationService $invitationService
    ) {}

    public function key(): RegistrationFlow
    {
        return RegistrationFlow::USER_JOINS_USER_ORG;
    }

    public function create(RegistrationData $data): void
    {
        $organization = $this->organizationService->find($data->organization);

        if (! $organization) {
            throw ValidationException::withMessages([
                'organization' => 'Invalid organization selected',
            ]);
        }

        if ($organization->registration_mode === RegistrationMode::INVITE_ONLY) {
            $this->invitationService->validateOrFail($data->invite_code);
        }

        $data->organization = $organization;
    }
}
