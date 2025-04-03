<?php

namespace App\Authorization;

use App\Contracts\Repositories\SystemSettingRepository;
use App\Data\RegistrationData;
use App\Enums\RegistrationFlow;
use App\Models\User;
use Kwidoo\Contacts\Contracts\ContactRepository;
use Kwidoo\Mere\Contracts\Authorizer;
use Spatie\LaravelData\Data;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class RegistrationAuthorizer implements Authorizer
{
    public function __construct(
        protected ContactRepository $contactRepository,
        protected SystemSettingRepository $settingRepository
    ) {}

    public function authorize(string $ability, mixed $resource, Data $extra): void
    {
        if ($ability !== 'registerNewUser' || $resource !== 'registration' || !($extra instanceof RegistrationData)) {
            throw new InvalidArgumentException('Invalid ability or resource type.');
        }

        $organization = $extra->organization;
        $identity = $extra->method;
        $user = $this->retrieveUser($identity, $extra->value);

        $this->denyIfCreatingOrgWhenForcedToUseMain($extra->flow);
        $this->denyIfInviteRequiredButMissing($organization, $extra->inviteCode);
        $this->denyIfIdentityUsedInOrg($user, $organization, $identity, $extra->value);
        $this->denyIfIdentityReuseForbidden($user, $identity, $extra->value);
        $this->denyIfIdentityAlreadyRegistered($user, $identity, $extra->value);
    }

    protected function denyIfCreatingOrgWhenForcedToUseMain(string $flow): void
    {
        if (
            $this->settingBool('registration.force_main_org', 'iam.defaults.force_main_org') &&
            $flow === RegistrationFlow::USER_CREATES_ORG
        ) {
            throw ValidationException::withMessages([
                'organization' => __('Cannot create organization. Only main organization is allowed.'),
            ]);
        }
    }

    protected function denyIfInviteRequiredButMissing(?object $org, ?string $inviteCode): void
    {
        if (
            $this->settingBool('registration.enforce_invite', 'iam.defaults.enforce_invite_code') &&
            $org?->registration_mode->isInviteOnly() &&
            empty($inviteCode)
        ) {
            throw ValidationException::withMessages([
                'invite_code' => __('Invite code is required for this :organization.', ['organization' => $org->name]),
            ]);
        }
    }

    protected function denyIfIdentityUsedInOrg(?User $user, ?object $org, string $identity, string $value): void
    {
        if (
            $user &&
            ($user->organizations->contains($org) || $user->ownedOrganizations->contains($org))
        ) {
            throw ValidationException::withMessages([
                $identity => __("This :$identity is already used in this organization. Please log in.", [$identity => $value]),
            ]);
        }
    }

    protected function denyIfIdentityReuseForbidden(?User $user, string $identity, string $value): void
    {
        if (
            $user &&
            $user->ownedOrganizations->isNotEmpty() &&
            !$this->settingBool('registration.allow_identity_reuse_across_orgs', 'iam.defaults.allow_duplicate_identity_across_orgs')
        ) {
            throw ValidationException::withMessages([
                $identity => __("This :$identity is already used as an owner in another organization. Please log in.", [$identity => $value]),
            ]);
        }
    }

    protected function denyIfIdentityAlreadyRegistered(?User $user, string $identity, string $value): void
    {
        if ($user) {
            throw ValidationException::withMessages([
                $identity => __("This :$identity is already registered. Please log in.", [$identity => $value]),
            ]);
        }
    }

    protected function retrieveUser(string $identity, string $value): ?User
    {
        return $this->contactRepository
            ->where('value', $value)
            ->where('type', $identity)
            ->first()
            ?->contactable;
    }

    protected function settingBool(string $key, string $configKey): bool
    {
        $setting = $this->settingRepository->findByField('key', $key)->first();

        return $setting !== null
            ? filter_var($setting->value, FILTER_VALIDATE_BOOLEAN)
            : config($configKey, false);
    }
}
