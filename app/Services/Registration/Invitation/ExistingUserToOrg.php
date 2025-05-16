<?php

namespace App\Services\Registration\Invitation;

use Kwidoo\Mere\Contracts\Repositories\OrganizationRepository;
use App\Contracts\Services\InvitationStrategy;
use App\Data\InvitationData;
use App\Data\InvitationAcceptanceData;
use App\Resolvers\UserResolver;
use Illuminate\Validation\ValidationException;
use Kwidoo\Mere\Contracts\Models\InvitationInterface;
use Kwidoo\Mere\Contracts\Models\UserInterface;
use Kwidoo\Mere\Contracts\Repositories\InvitationRepository;
use Exception;

class ExistingUserToOrg extends BaseInvitation implements InvitationStrategy
{
    public function __construct(
        protected InvitationRepository $invitationRepository,
        protected OrganizationRepository $repository,
        protected UserResolver $resolver,
    ) {
        parent::__construct($invitationRepository);
    }

    /**
     * @param InvitationData $data
     *
     * @return \App\Models\Invitation
     */
    public function send(InvitationData $data): InvitationInterface
    {
        $user = $this->resolver->resolve($data->method, $data->value);

        if (!$user) {
            throw ValidationException::withMessages(['value' => 'User not found']);
        }

        /** @var \App\Models\Invitation */
        $invitation = $this->create($data);

        $this->notifyUser($invitation, $data);

        return $invitation;
    }

    /**
     * @param InvitationAcceptanceData $data
     *
     * @return UserInterface
     */
    public function accept(InvitationAcceptanceData $data): UserInterface
    {
        /** @var \App\Models\Invitation */
        $invitation = $this->invitationRepository->findByToken($data->token);

        if (!$invitation) {
            throw new Exception("Invitation not found");
        }
        if ($invitation->isAccepted()) {
            throw new Exception("Invitation already accepted");
        }
        if ($invitation->isExpired()) {
            throw new Exception("Invitation expired");
        }

        $organization = $this->repository->find($invitation->organization_id);

        $this->repository->attachUser($organization, $data->acceptingUser, $invitation->role);

        return $data->acceptingUser ?? throw new Exception("No acceptance needed for existing users");
    }
}
