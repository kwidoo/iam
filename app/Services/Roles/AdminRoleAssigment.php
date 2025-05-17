<?php

namespace App\Services\Roles;

class AdminRoleAssignment extends BaseRoleAssignmentService
{
    /**
     * The key of the role assignment strategy.
     *
     * @var string
     */
    public function roleType(): string
    {
        return 'admin';
    }
}
