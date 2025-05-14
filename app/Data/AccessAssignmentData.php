<?php

namespace App\Data;

use App\Enums\RegistrationFlow;
use Spatie\LaravelData\Data;
use Kwidoo\Mere\Contracts\Data\AccessAssignmentData;
use Kwidoo\Mere\Contracts\Models\UserInterface;

class DefaultAccessAssignmentData extends Data implements AccessAssignmentData
{
    public function __construct(
        public string $actor, // 'self', 'admin', 'super_admin'
        public UserInterface $user,
        public RegistrationFlow $flow,
    ) {}
}
