<?php

namespace App\Contracts\Services;


use App\Data\InvitationData;
use App\Data\InvitationAcceptanceData;
use App\Models\User;

interface InvitationStrategy
{
    public function send(InvitationData $data): void;

    public function accept(InvitationAcceptanceData $data): User;
}
