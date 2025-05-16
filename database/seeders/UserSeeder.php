<?php

namespace Database\Seeders;

use App\Contracts\Services\Registration\RegistrationService;
use App\Data\Registration\DefaultRegistrationData;
use App\Enums\OrganizationFlow;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function __construct(
        private RegistrationService $registrationService
    ) {
        // Constructor injection of RegistrationService
    }
    /**
     * Seed sample users with appropriate roles.
     */
    public function run(): void
    {
        // Create super-admin if it doesn't exist
        $superAdmin = $this->registrationService->registerNewUser(DefaultRegistrationData::from([
            'value' => 'super@example.com',
            'otp' => false,
            'type' => 'email',
            'method' => 'email',
            'password' => bcrypt('password'),
            'fname' => 'Super',
            'lname' => 'Admin',
            'dob' => '1975-03-25',
            'gender' => 'm',
            'flow' => OrganizationFlow::INITIAL_BOOTSTRAP->value,
        ]));

        $superAdminRole = Role::where('name', 'super-admin')->first();
        $superAdmin->assignRole($superAdminRole);


        // Create admin user
        $admin = $this->registrationService->registerNewUser(DefaultRegistrationData::from([
            'value' => 'admin@example.com',
            'otp' => false,
            'type' => 'email',
            'method' => 'email',
            'password' => bcrypt('password'),
            'fname' => 'Admin',
            'lname' => 'User',
            'dob' => '1980-01-01',
            'gender' => 'm',
            'flow' => OrganizationFlow::USER_CREATES_ORG->value,
        ]));
        $organization = $admin->ownedOrganizations()->first();
        $adminRole = Role::where('name', 'admin')->first();
        $admin->assignRole($adminRole);

        // Create manager user
        $manager = $this->registrationService->registerNewUser(DefaultRegistrationData::from([
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
            'flow' => OrganizationFlow::USER_JOINS_USER_ORG->value,
        ]));

        $managerRole = Role::where('name', 'manager')->first();
        $manager->assignRole($managerRole);

        // Create staff user
        $staff = $this->registrationService->registerNewUser(DefaultRegistrationData::from([
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
            'flow' => OrganizationFlow::USER_JOINS_USER_ORG->value,
        ]));

        $staffRole = Role::where('name', 'staff')->first();
        $staff->assignRole($staffRole);

        // Create regular user
        $user = $this->registrationService->registerNewUser(DefaultRegistrationData::from([
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
            'flow' => OrganizationFlow::USER_JOINS_USER_ORG->value,
        ]));

        $userRole = Role::where('name', 'user')->first();
        $user->assignRole($userRole);
    }
}
