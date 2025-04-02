<?php

namespace App\Authorization;

use App\Contracts\Repositories\SystemSettingRepository;
use App\Data\RegistrationData;
use App\Enums\RegistrationFlow;
use Illuminate\Validation\ValidationException;
use Kwidoo\Contacts\Contracts\ContactRepository;
use Kwidoo\Mere\Contracts\Authorizer;
use Spatie\LaravelData\Data;

class RegistrationAuthorizer implements Authorizer
{
    public function __construct(
        protected ContactRepository $repository,
        protected SystemSettingRepository $settingRepository
    ) {}

    public function authorize(string $ability, mixed $resource, Data|array|null $extra = null)
    {
        if ($ability !== 'registerNewUser' || !($extra instanceof RegistrationData)) {
            return;
        }

        $flow = $extra->flow;
        $org = $extra->organization;

        if (
            $this->settingBool('registration.force_main_org', 'iam.defaults.force_main_org')
            && $flow === RegistrationFlow::USER_CREATES_ORG
        ) {
            throw ValidationException::withMessages([
                'organization' => 'Cannot create organization. Only main org is allowed.',
            ]);
        }

        if (
            $this->settingBool('registration.enforce_invite', 'iam.defaults.enforce_invite_code')
            && $org?->registration_mode->isInviteOnly()
            && empty($extra->inviteCode)
        ) {
            throw ValidationException::withMessages([
                'invite_code' => 'Invite code is required for this organization.',
            ]);
        }

        $existing = $this->repository
            ->where('value', $extra->value)
            ->where('type', $extra->method)
            ->first();

        $user = $existing?->contactable;

        if (
            $user &&
            ($user->organizations->contains($org) || $user->ownedOrganizations->contains($org))
        ) {
            throw ValidationException::withMessages([
                'identity' => 'This identity is already used in this organization. Please log in.',
            ]);
        }

        if (
            $user &&
            $user->ownedOrganizations->isNotEmpty() &&
            !$this->settingBool('registration.allow_identity_reuse_across_orgs', 'iam.defaults.allow_duplicate_identity_across_orgs')
        ) {
            throw ValidationException::withMessages([
                'identity' => 'This identity is already used as an owner in another organization.',
            ]);
        }
    }

    protected function settingBool(string $key, string $configKey): bool
    {
        $setting = $this->settingRepository->findByField("key", $key)->first();
        return $setting !== null
            ? filter_var($setting->value, FILTER_VALIDATE_BOOLEAN)
            : config($configKey, false);
    }
}
