<?php

namespace App\Authorizers;

use App\Contracts\Repositories\OrganizationRepository;
use App\Contracts\Repositories\SystemSettingRepository;
use App\Contracts\Repositories\UserRepository;
use App\Data\RegistrationData;
use App\Enums\RegistrationFlow;
use App\Models\Organization;
use App\Models\User;
use App\Resolvers\UserResolver;
use Kwidoo\Contacts\Contracts\ContactRepository;
use Kwidoo\Mere\Contracts\Authorizer;
use Spatie\LaravelData\Data;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class RegistrationAuthorizer implements Authorizer
{
    public function __construct(
        protected ContactRepository $contactRepository,
        protected SystemSettingRepository $settingRepository,
        protected OrganizationRepository $repository,
        protected UserRepository $userRepository,
        protected UserResolver $userResolver,
    ) {}

    public function authorize(string $ability, mixed $resource, Data $extra): void
    {
        if ($ability !== 'registerNewUser' || !in_array($resource,  ['registration', 'registration-invite']) || !($extra instanceof RegistrationData)) {
            throw new InvalidArgumentException('Invalid ability or resource type.');
        }

        if ($extra->flow === RegistrationFlow::INITIAL_BOOTSTRAP && ($this->repository->count() > 0 || $this->userRepository->count() > 1)) {
            throw ValidationException::withMessages([
                'organization' => __('Initial organization creation is not allowed anymore.'),
            ]);
        }

        $organization = $extra->organization;
        $identity = $extra->method;
        $user = $this->retrieveUser($identity, $extra->value);

        $this->denyIfCreatingOrgWhenForcedToUseMain($extra->flow);
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

    protected function denyIfIdentityUsedInOrg(?User $user, ?Organization $organization, string $identity, string $value): void
    {
        if (
            $user &&
            ($user->organizations->contains($organization) || $user->ownedOrganizations->contains($organization))
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
        return $this->userResolver->resolve($identity, $value);
    }

    protected function settingBool(string $key, string $configKey): bool
    {
        $setting = $this->settingRepository->findByField('key', $key)->first();

        return $setting !== null
            ? filter_var($setting->value, FILTER_VALIDATE_BOOLEAN)
            : config($configKey, false);
    }
}
