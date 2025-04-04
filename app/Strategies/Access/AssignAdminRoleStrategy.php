<?php

namespace App\Access\Roles;

use App\Contracts\Access\RoleAssignmentStrategy;
use App\Models\User;
use App\Models\Organization;
use App\Factories\RoleServiceFactory;
use Kwidoo\Mere\Contracts\Lifecycle;

class AssignAdminRoleStrategy implements RoleAssignmentStrategy
{
    public function __construct(
        protected RoleServiceFactory $factory,
        protected Lifecycle $lifecycle,
    ) {}

    public function assign(User $user, Organization $organization): void
    {
        $slug = $organization->slug;

        $roleService = $this->factory->make($this->lifecycle->withoutAuth());
        $role = $roleService->getByName("{$slug}-admin", $organization->id);
        $roleService->assignRole($role, $user->id, $organization->id);
    }
}
