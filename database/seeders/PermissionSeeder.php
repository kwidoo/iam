<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use App\Services\OrganizationAccessProvider;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default global roles
        $this->createGlobalRoles();

        // Create default global permissions
        $this->createGlobalPermissions();

        // Setup organization-specific roles and permissions
        $this->setupOrganizationPermissions();
    }

    /**
     * Create global roles.
     *
     * @return void
     */
    protected function createGlobalRoles(): void
    {
        // Super Admin - has all powers across the system
        Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'web',
        ]);

        // Regular User - default role for all users
        Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'web',
        ]);
    }

    /**
     * Create global permissions.
     *
     * @return void
     */
    protected function createGlobalPermissions(): void
    {
        // Define resources and their permissions
        $resourcePermissions = [
            'user' => ['view', 'list', 'create', 'edit', 'delete'],
            'organization' => ['view', 'list', 'create', 'edit', 'delete'],
            'role' => ['view', 'list', 'create', 'edit', 'delete'],
            'permission' => ['view', 'list', 'create', 'edit', 'delete'],
        ];

        // Create all global permissions
        foreach ($resourcePermissions as $resource => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$resource}:{$action}",
                    'guard_name' => 'web',
                ]);
            }
        }

        // Assign global permissions to super-admin role
        $superAdminRole = Role::where('name', 'super-admin')->first();
        if ($superAdminRole) {
            $allPermissions = Permission::where('name', 'not like', 'org-%')->get();
            $superAdminRole->syncPermissions($allPermissions);
        }

        // Assign limited permissions to the user role
        $userRole = Role::where('name', 'user')->first();
        if ($userRole) {
            $userRole->syncPermissions([
                Permission::findByName('user:view'),
                Permission::findByName('organization:view'),
                Permission::findByName('organization:list'),
                Permission::findByName('organization:create'),
            ]);
        }
    }

    /**
     * Setup permissions for existing organizations.
     *
     * @return void
     */
    protected function setupOrganizationPermissions(): void
    {
        // $accessProvider = app(OrganizationAccessProvider::class);

        // // For each organization, set up default permissions and sync them to users
        // Organization::all()->each(function ($organization) use ($accessProvider) {
        //     $accessProvider->setupOrganizationDefaultPermissions($organization);
        //     $accessProvider->syncOrganizationUsers($organization);
        // });

        // // Assign super-admin to admin of all organizations
        // $superAdmins = User::role('super-admin')->get();
        // foreach ($superAdmins as $superAdmin) {
        //     Organization::all()->each(function ($organization) use ($superAdmin) {
        //         if (!$superAdmin->organizations->contains($organization->id)) {
        //             $organization->users()->attach($superAdmin->id, ['role' => 'admin']);
        //         }
        //     });
        // }
    }
}
