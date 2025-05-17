<?php

namespace App\Services\Roles;

class StandardUserRoleAssignment extends BaseRoleAssignmentService
{
    /**
     * The key of the role assignment strategy.
     *
     * @var string
     */
    public function roleType(): string
    {
        return 'default';
    }
}
