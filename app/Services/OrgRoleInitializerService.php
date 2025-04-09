<?php


namespace App\Services;

use App\Data\RegistrationData;
use App\Factories\PermissionServiceFactory;
use App\Factories\RoleServiceFactory;
use App\Models\Organization;
use Kwidoo\Mere\Contracts\Lifecycle;

class OrgRoleInitializerService
{
    public function __construct(
        protected RoleServiceFactory $rsf,
        protected PermissionServiceFactory $psf,
    ) {}

    public function createDefaults(Organization $organization, RegistrationData $data, Lifecycle $lifecycle): void
    {
        $slug = $organization->slug;

        $roleService = $this->rsf->make($lifecycle);

        // Create Roles
        $roleService->create([
            'name' => "{$slug}-admin",
            'organization_id' => $organization->id,
            //  'description' => "{$data->fname} {$data->lname}'s organization admin role.",
            'guard_name' => 'web',
        ]);

        $roleService->create([
            'name' => "{$slug}-user",
            'organization_id' => $organization->id,
            //   'description' => "Default user role for {$data->fname} {$data->lname}'s organization.",
            'guard_name' => 'web',
        ]);

        $permissionService = $this->psf->make($lifecycle);

        // Create Permissions
        $permissionService->create([
            'name' => "{$slug}-admin",
            'organization_id' => $organization->id,
            //     'description' => "Full access in {$data->fname} {$data->lname}'s organization.",
            'guard_name' => 'web',
        ]);

        $permissionService->create([
            'name' => "{$slug}-user",
            'organization_id' => $organization->id,
            //    'description' => "Default user permissions for organization.",
            'guard_name' => 'web',
        ]);
    }
}
