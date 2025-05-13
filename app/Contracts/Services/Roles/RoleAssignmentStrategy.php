<?php

namespace App\Contracts\Services\Roles;

use App\Data\RoleAssignmentData;

interface RoleAssignment
{
    public function assign(RoleAssignmentData $data): void;
    public function revoke(RoleAssignmentData $data): void;
}
