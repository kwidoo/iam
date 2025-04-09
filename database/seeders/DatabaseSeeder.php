<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Organization and super admin are already created in your existing setup

        // Run the seeders in order of dependency
        $this->call([
            OauthClientsTableSeeder::class, // Create OAuth clients first
            MenuItemSeeder::class,     // First create menu structure
            ResourceConfigSeeder::class, // Then create resource configurations
            RoleSeeder::class,         // Then create roles
            PermissionSeeder::class,   // Then create and assign permissions
            UserSeeder::class,         // Finally create sample users with roles
        ]);
    }
}
