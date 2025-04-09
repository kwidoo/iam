<?php

namespace App\Strategies\Invitation;

use App\Contracts\Repositories\OrganizationRepository;
use App\Contracts\Repositories\UserRepository;
use App\Contracts\Services\InvitationStrategy;
use App\Data\InvitationData;
use App\Data\InvitationAcceptanceData;
use App\Models\User;
use App\Resolvers\UserResolver;
use App\Services\ContactService;
use Exception;
use Illuminate\Validation\ValidationException;

class InviteExistingUserToOrgStrategy implements InvitationStrategy
{
    public function __construct(
        protected OrganizationRepository $repository,
        protected UserResolver $resolver,
    ) {
    }

    public function send(InvitationData $data): void
    {
        $user = $this->resolver->resolve($data->method, $data->value);

        if (!$user) {
            throw ValidationException::withMessages(['value' => 'User not found']);
        }

        $organization = $this->repository->find($data->organizationId);
        $organization->users()->syncWithoutDetaching([$user->id => ['role' => $data->role]]);
    }

    public function accept(InvitationAcceptanceData $data): User
    {
        return $data->acceptingUser ?? throw new Exception("No acceptance needed for existing users");
    }
}
