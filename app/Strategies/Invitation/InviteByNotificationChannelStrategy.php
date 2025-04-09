<?php

namespace App\Strategies\Invitation;

use App\Contracts\Repositories\InvitationRepository;
use App\Contracts\Services\InvitationStrategy;
use App\Data\InvitationData;
use App\Data\InvitationAcceptanceData;
use App\Models\User;
use App\Notifications\GenericInvitationNotification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;

class InviteByNotificationChannelStrategy implements InvitationStrategy
{
    public function __construct(
        protected InvitationRepository $invitationRepository,
    ) {
    }

    public function send(InvitationData $data): void
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

        Notification::route($data->method, $data->value)
            ->notify(new GenericInvitationNotification($invitation));
    }

    public function accept(InvitationAcceptanceData $data): User
    {
        $invitation = $this->invitationRepository->findByToken($data->token);
        return $this->invitationRepository->accept($invitation, $data->acceptingUser);
    }
}
