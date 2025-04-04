<?php

namespace App\Authorization;

use App\Authorizers\RegistrationAuthorizer;
use App\Contracts\Repositories\InvitationRepository;
use App\Contracts\Repositories\SystemSettingRepository;
use App\Models\Organization;;

use App\Services\OrganizationService;
use Kwidoo\Mere\Contracts\Authorizer;
use Spatie\LaravelData\Contracts\BaseData;
use Illuminate\Support\Facades\Gate;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Validation\ValidationException;

class InvitationAuthorizer implements Authorizer
{
    public function __construct(
        protected OrganizationService $organizationService,
        protected RegistrationAuthorizer $baseAuthorizer,
        protected SystemSettingRepository $settingRepository,
        protected InvitationRepository $invitationRepository,
        protected AuthFactory $auth,
        protected ?string $guard = null
    ) {}

    public function authorize(string $ability, mixed $resource, BaseData $extra): void
    {
        if ($ability === 'sendInvite') {
            $this->canSendInvite($extra->organization);
        }

        if ($ability === 'registerNewUser') {
            $this->denyIfInviteRequiredButMissing($extra->organization, $extra->inviteCode);
            $this->baseAuthorizer->authorize($ability, $resource, $extra);
        }
    }

    /**
     * @param Organization $organization
     *
     * @return void
     */
    public function canSendInvite(Organization $organization): void
    {
        $guardInstance = $this->guard
            ? $this->auth->guard($this->guard)
            : $this->auth->guard();

        if ($organization->registration_mode->isInviteOnly() && $guardInstance->check()) {
            Gate::authorize('invite-users', [
                'organization' => $organization
            ]);
        }
    }

    protected function denyIfInviteRequiredButMissing(?Organization $organization, ?string $inviteCode): void
    {
        /** @var User */
        $user = $this->auth->guard()->user();

        if (
            $user &&
            (
                $user->hasRole('super-admin') ||
                $user->hasRole("{$organization->slug}-admin"))
        ) {
            return;
        }

        if (
            ($this->settingBool('registration.enforce_invite', 'iam.defaults.enforce_invite_code') ||
                $organization?->registration_mode->isInviteOnly()) &&
            empty($inviteCode)
        ) {
            throw ValidationException::withMessages([
                'invite_code' => __('Invite code is required for this :organization.', ['organization' => $organization->name]),
            ]);
        }

        if (! empty($inviteCode)) {
            $invitation = $this->invitationRepository->findByToken($inviteCode);

            if (! $invitation || $invitation->organization_id !== $organization?->id) {
                throw ValidationException::withMessages([
                    'invite_code' => __('Invalid or expired invite code.'),
                ]);
            }
        }
    }

    protected function settingBool(string $key, string $configKey): bool
    {
        $setting = $this->settingRepository->findByField('key', $key)->first();

        return $setting !== null
            ? filter_var($setting->value, FILTER_VALIDATE_BOOLEAN)
            : config($configKey, false);
    }
}
