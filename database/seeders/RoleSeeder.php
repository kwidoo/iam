<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Seed system roles.
     */
    public function run(): void
    {
        // Create system roles
        // The super-admin role is assumed to already exist from your existing setup

        // General system roles
        $roles = [
            'admin' => 'Administrator with access to manage most system resources',
            'manager' => 'Manager with limited management capabilities',
            'staff' => 'Staff member with basic access to perform day-to-day operations',
            'user' => 'Standard user with minimal access'
        ];

        foreach ($roles as $roleName => $description) {
            Role::updateOrCreate(
                ['name' => $roleName],
                ['guard_name' => 'web']
            );
        }

        // Check if super-admin exists, create if it doesn't
        // This is usually created in your existing setup, but adding as a fallback
        if (!Role::where('name', 'super-admin')->exists()) {
            Role::create([
                'name' => 'super-admin',
                'guard_name' => 'web',
            ]);
        }
    }
}
