<?php

namespace App\Strategies\Access;

use App\Contracts\Access\RoleAssignmentStrategy;
use App\Models\User;
use App\Models\Organization;
use App\Factories\RoleServiceFactory;
use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;
use Kwidoo\Lifecycle\Data\LifecycleOptionsData;

class AssignAdminRoleStrategy implements RoleAssignmentStrategy
{
    protected LifecycleOptionsData $options;

    public function __construct(
        protected RoleServiceFactory $factory,
        protected Lifecycle $lifecycle,
    ) {
        $this->options = new LifecycleOptionsData();
    }

    public function assign(User $user, Organization $organization): void
    {
        $slug = $organization->slug;
        $roleService = $this->factory->make($this->lifecycle);
        $role = $roleService->getByName("{$slug}-admin", $organization->id);
        $roleService->assignRole($role, $user->id, $organization->id);
    }
}
