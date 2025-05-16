<?php

namespace App\Services\Registration\Invitation;

use App\Contracts\Services\InvitationStrategy;
use App\Data\InvitationData;
use App\Notifications\GenericInvitationNotification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;
use Kwidoo\Mere\Contracts\Models\InvitationInterface;

/**
 * @property \Kwidoo\Mere\Contracts\Repositories\InvitationRepository $invitationRepository
 */
abstract class BaseInvitation implements InvitationStrategy
{
    protected function notifyUser(InvitationInterface $invitation, InvitationData $data)
    {
        Notification::route($data->method, $data->value)
            ->notify(new GenericInvitationNotification($invitation));
    }

    protected function create(InvitationData $data): InvitationInterface
    {
        return $this->invitationRepository->create(
            [
                'method' => $data->method,
                'value' => $data->value,
                'token' => Str::uuid()->toString(),
                'organization_id' => $data->organizationId,
                'role' => $data->role,
            ]
        );
    }
}
