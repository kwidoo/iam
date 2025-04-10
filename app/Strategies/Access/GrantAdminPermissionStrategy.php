<?php

namespace App\Strategies\Access;

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
    ) {
    }

    public function assign(User $user, Organization $organization): void
    {
        $slug = $organization->slug;
        $permissionService = $this->factory->make($this->lifecycle->withoutAuth());
        $permission = $permissionService->getByName("{$slug}-admin", $organization->id);
        $permissionService->givePermission($permission, $user->id, $organization->id);
    }
}
