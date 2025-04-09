<?php

namespace Database\Seeders;

use App\Enums\RegistrationFlow;
use App\Models\User;
use App\Services\RegistrationService;
use App\Data\RegistrationData;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Seed sample users with appropriate roles.
     */
    public function run(): void
    {
        // Create super-admin if it doesn't exist
        $superAdmin = app()->make(RegistrationService::class)->registerNewUser(RegistrationData::from([
            'value' => 'super@example.com',
            'otp' => false,
            'type' => 'email',
            'method' => 'email',
            'password' => bcrypt('password'),
            'fname' => 'Super',
            'lname' => 'Admin',
            'dob' => '1975-03-25',
            'gender' => 'm',
            'flow' => RegistrationFlow::INITIAL_BOOTSTRAP->value,
        ]));

        $superAdminRole = Role::where('name', 'super-admin')->first();
        $superAdmin->assignRole($superAdminRole);


        // Create admin user
        $admin = app()->make(RegistrationService::class)->registerNewUser(RegistrationData::from([
            'value' => 'admin@example.com',
            'otp' => false,
            'type' => 'email',
            'method' => 'email',
            'password' => bcrypt('password'),
            'fname' => 'Admin',
            'lname' => 'User',
            'dob' => '1980-01-01',
            'gender' => 'm',
            'flow' => RegistrationFlow::USER_CREATES_ORG->value,
        ]));
        $organization = $admin->ownedOrganizations()->first();
        $adminRole = Role::where('name', 'admin')->first();
        $admin->assignRole($adminRole);

        // Create manager user
        $manager = app()->make(RegistrationService::class)->registerNewUser(RegistrationData::from([
            'value' => 'manager@example.com',
            'otp' => false,
            'type' => 'email',
            'method' => 'email',
            'password' => bcrypt('password'),
            'fname' => 'Manager',
            'lname' => 'User',
            'dob' => '1985-02-15',
            'gender' => 'm',
            'org_name' => $organization->slug,
            'flow' => RegistrationFlow::USER_JOINS_USER_ORG->value,
        ]));

        $managerRole = Role::where('name', 'manager')->first();
        $manager->assignRole($managerRole);

        // Create staff user
        $staff = app()->make(RegistrationService::class)->registerNewUser(RegistrationData::from([
            'value' => 'staff@example.com',
            'otp' => false,
            'type' => 'email',
            'method' => 'email',
            'password' => bcrypt('password'),
            'fname' => 'Staff',
            'lname' => 'User',
            'dob' => '1990-05-20',
            'gender' => 'f',
            'org_name' => $organization->slug,
            'flow' => RegistrationFlow::USER_JOINS_USER_ORG->value,
        ]));

        $staffRole = Role::where('name', 'staff')->first();
        $staff->assignRole($staffRole);

        // Create regular user
        $user = app()->make(RegistrationService::class)->registerNewUser(RegistrationData::from([
            'value' => 'user@example.com',
            'otp' => false,
            'type' => 'email',
            'method' => 'email',
            'password' => bcrypt('password'),
            'fname' => 'Regular',
            'lname' => 'User',
            'dob' => '1995-10-10',
            'gender' => 'f',
            'org_name' => $organization->slug,
            'flow' => RegistrationFlow::USER_JOINS_USER_ORG->value,
        ]));

        $userRole = Role::where('name', 'user')->first();
        $user->assignRole($userRole);
    }
}
