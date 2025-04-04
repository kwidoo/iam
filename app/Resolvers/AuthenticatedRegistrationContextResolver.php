<?php

namespace App\Resolvers;

use App\Data\AccessAssignmentData;
use App\Enums\RegistrationFlow;
use App\Models\User;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Kwidoo\Mere\Contracts\AccessAssignmentContextResolver;
use Kwidoo\Mere\Contracts\AccessAssignmentData as AccessAssignmentDataContract;
use Spatie\LaravelData\Contracts\BaseData;

class AuthenticatedRegistrationContextResolver implements AccessAssignmentContextResolver
{
    public function __construct(
        protected AuthFactory $auth,
        protected string $guard = 'api',
    ) {}

    /**
     * @param \App\Data\RegistrationData $data
     *
     * @return AccessAssignmentDataContract
     */
    public function resolve(BaseData $data): AccessAssignmentDataContract
    {
        $guardInstance = $this->guard
            ? $this->auth->guard($this->guard)
            : $this->auth->guard();

        $user = $guardInstance->user();

        $actor = 'admin';
        if ($user instanceof User && $user->hasRole('super-admin')) {
            $actor = 'super_admin';
        }

        return new AccessAssignmentData(
            actor: $actor,
            user: $data->user,
            flow: RegistrationFlow::from($data->flow),
        );
    }
}
