<?php

namespace App\Contracts\Access;

use App\Models\User;
use App\Models\Organization;

interface PermissionAssignmentStrategy
{
    public function assign(User $user, Organization $organization): void;
}
