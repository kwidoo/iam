<?php

namespace App\Services\Permissions;

class AdminPermissionAssignment extends BasePermissionAssignmentService
{
    public function permissionType(): string
    {
        return 'admin';
    }
}
