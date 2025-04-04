<?php

namespace App\Access\Permissions;

use App\Contracts\Access\PermissionAssignmentStrategy;
use App\Models\User;
use App\Models\Organization;
use App\Factories\PermissionServiceFactory;
use Kwidoo\Mere\Contracts\Lifecycle;

class GrantAdminPermissionStrategy implements PermissionAssignmentStrategy
{
    public function __construct(
        protected PermissionServiceFactory $factory,
        protected Lifecycle $lifecycle,
    ) {}

    public function assign(User $user, Organization $organization): void
    {
        $permissionService = $this->factory->make($this->lifecycle->withoutAuth());
        $permission = $permissionService->getByName('admin', $organization->id);
        $permissionService->givePermission($permission, $user->id);
    }
}
