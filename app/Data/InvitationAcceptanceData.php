<?php

namespace App\Data;

use App\Models\User;
use Spatie\LaravelData\Data;

class InvitationAcceptanceData extends Data
{
    public function __construct(
        public string $token,
        public ?User $acceptingUser = null,
    ) {}
}
