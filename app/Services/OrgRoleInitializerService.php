<?php


namespace App\Services;

use App\Contracts\Services\PermissionService;
use App\Contracts\Services\RoleService;
use App\Data\RegistrationData;
use App\Models\Organization;

class OrgRoleInitializerService
{
    public function __construct(
        protected RoleService $roleService,
        protected PermissionService $permissionService,
    ) {}

    public function createDefaults(Organization $organization, RegistrationData $data): void
    {
        $slug = $organization->slug;

        // Create Roles
        $this->roleService->create([
            'name' => "{$slug}-admin",
            'organization_id' => $organization->id,
            'description' => "{$data->fname} {$data->lname}'s organization admin role.",
            'guard_name' => 'web',
        ]);

        $this->roleService->create([
            'name' => "{$slug}-user",
            'organization_id' => $organization->id,
            'description' => "Default user role for {$data->fname} {$data->lname}'s organization.",
            'guard_name' => 'web',
        ]);

        // Create Permissions
        $this->permissionService->create([
            'name' => "{$slug}-admin",
            'organization_id' => $organization->id,
            'description' => "Full access in {$data->fname} {$data->lname}'s organization.",
            'guard_name' => 'web',
        ]);

        $this->permissionService->create([
            'name' => "{$slug}-user",
            'organization_id' => $organization->id,
            'description' => "Default user permissions for organization.",
            'guard_name' => 'web',
        ]);
    }
}
