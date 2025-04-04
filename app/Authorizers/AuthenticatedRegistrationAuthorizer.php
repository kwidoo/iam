<?php

namespace App\Authorizers;

use App\Authorization\InvitationAuthorizer;
use Illuminate\Support\Facades\Gate;
use Kwidoo\Mere\Contracts\Authorizer;
use Spatie\LaravelData\Data;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Validation\ValidationException;

class AuthenticatedRegistrationAuthorizer implements Authorizer
{
    public function __construct(
        protected InvitationAuthorizer $baseAuthorizer,
        protected AuthFactory $auth,
        protected ?string $guard = null
    ) {}

    /**
     * Authorize the given ability and resource.
     * @param string $ability
     * @param mixed $resource
     * @param \App\Data\RegistrationData $extra
     * @return void
     * @throws InvalidArgumentException
     * @throws ValidationException
     */
    public function authorize(string $ability, mixed $resource, Data $extra): void
    {
        $guardInstance = $this->guard
            ? $this->auth->guard($this->guard)
            : $this->auth->guard();

        if ($guardInstance->check()) {
            Gate::authorize('register-users', [
                'organization' => $extra->organization
            ]);
        }

        $this->baseAuthorizer->authorize($ability, $resource, $extra);
    }
}
