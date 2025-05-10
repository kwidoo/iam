<?php

namespace App\Strategies\Access;

use App\Contracts\Access\PermissionAssignmentStrategy;
use App\Models\User;
use App\Models\Organization;
use App\Factories\PermissionServiceFactory;
use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;
use Kwidoo\Lifecycle\Data\LifecycleOptionsData;

class GrantDefaultUserPermissionStrategy implements PermissionAssignmentStrategy
{
    protected LifecycleOptionsData $options;

    public function __construct(
        protected PermissionServiceFactory $factory,
        protected Lifecycle $lifecycle,
    ) {
        $this->options = new LifecycleOptionsData();
    }

    public function assign(User $user, Organization $organization): void
    {
        $slug = $organization->slug;
        $options = $this->options->withoutAuth();
        $permissionService = $this->factory->make($this->lifecycle);
        $permission = $permissionService->getByName("{$slug}-user", $organization->id);
        if ($permission) {
            $permissionService->givePermission($permission, $user->id, $organization->id);
        }
    }
}
