<?php

namespace App\Services\Permissions;

class StandardUserPermissionAssignment extends BasePermissionAssignmentService
{
    public function permissionType(): string
    {
        return 'default';
    }
}
