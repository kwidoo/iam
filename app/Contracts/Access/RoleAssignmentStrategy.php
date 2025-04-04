<?php

namespace App\Contracts\Access;

use App\Models\User;
use App\Models\Organization;

interface RoleAssignmentStrategy
{
    public function assign(User $user, Organization $organization): void;
}
