<?php

namespace App\Data;

use App\Enums\RegistrationFlow;
use App\Models\User;
use Spatie\LaravelData\Data;
use Kwidoo\Mere\Contracts\AccessAssignmentData as AccessAssignmentDataContract;

class AccessAssignmentData extends Data implements AccessAssignmentDataContract
{
    public function __construct(
        public string $actor, // 'self', 'admin', 'super_admin'
        public User $user,
        public RegistrationFlow $flow,
    ) {}
}
