<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Kwidoo\Mere\Models\MenuItem;

class MenuItemSeeder extends Seeder
{
    /**
     * Seed the menu structure.
     */
    public function run(): void
    {
        // Create main navigation sections
        $dashboard = MenuItem::create([
            'path' => '/dashboard',
            'name' => 'Dashboard',
            'component' => 'Dashboard',
        ]);

        // User management section
        $userManagement = MenuItem::create([
            'path' => '/user-management',
            'name' => 'UserManagement',
            'component' => 'RouterView',
        ]);

        // System management section
        $systemManagement = MenuItem::create([
            'path' => '/system',
            'name' => 'SystemManagement',
            'component' => 'RouterView',
        ]);

        // Create user management sub-items
        MenuItem::create([
            'path' => '/user-management/users',
            'name' => 'Users',
            'component' => 'RouterView',
            'parent_id' => $userManagement->id,
        ]);

        MenuItem::create([
            'path' => '/user-management/roles',
            'name' => 'Roles',
            'component' => 'RouterView',
            'parent_id' => $userManagement->id,
        ]);

        MenuItem::create([
            'path' => '/user-management/permissions',
            'name' => 'Permissions',
            'component' => 'RouterView',
            'parent_id' => $userManagement->id,
        ]);

        // Create system management sub-items
        MenuItem::create([
            'path' => '/system/organizations',
            'name' => 'Organizations',
            'component' => 'RouterView',
            'parent_id' => $systemManagement->id,
        ]);

        MenuItem::create([
            'path' => '/system/settings',
            'name' => 'Settings',
            'component' => 'Settings',
            'parent_id' => $systemManagement->id,
        ]);
    }
}
