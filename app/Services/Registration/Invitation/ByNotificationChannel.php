<?php

namespace App\Services\Registration\Invitation;

use Kwidoo\Mere\Contracts\Repositories\InvitationRepository;
use App\Contracts\Services\InvitationStrategy;
use App\Data\InvitationData;
use App\Data\InvitationAcceptanceData;
use Illuminate\Support\Str;
use Kwidoo\Mere\Contracts\Models\InvitationInterface;
use Kwidoo\Mere\Contracts\Models\UserInterface;

class ByNotificationChannel extends BaseInvitation implements InvitationStrategy
{
    public function __construct(
        protected InvitationRepository $invitationRepository,
    ) {}

    /**
     * @param InvitationData $data
     *
     * @return \App\Models\Invitation
     */
    public function send(InvitationData $data): InvitationInterface
    {
        $token = Str::uuid()->toString();

        $invitation = $this->invitationRepository->create(
            [
                'method' => $data->method,
                'value' => $data->value,
                'token' => $token,
                'organization_id' => $data->organizationId,
                'role' => $data->role,
            ]
        );

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
        $invitation = $this->invitationRepository->findByToken($data->token);
        return $this->invitationRepository->accept($invitation, $data->acceptingUser);
    }
}
