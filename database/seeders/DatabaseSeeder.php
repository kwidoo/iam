<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Kwidoo\Contacts\Contracts\ContactService;
use Kwidoo\Contacts\Contracts\VerificationService;
use Spatie\Permission\Models\Role;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(OauthClientsTableSeeder::class);

        $provider = config('auth.guards.api.provider');
        $model = config('auth.providers.' . $provider . '.model');
        if (!$model) {
            throw new RuntimeException('Unable to determine contact model from configuration.');
        }

        $user = $model::create([
            'password' => bcrypt('admin123'),
        ]);
        $contactService = app()->make(ContactService::class, ['model' => $user]);
        $uuid = $contactService->create('email', 'admin@example.com');

        $contactModel = config('contacts.model');
        if (!$contactModel) {
            throw new RuntimeException('Unable to determine contact model from configuration.');
        }

        $contact = $contactModel::find($uuid);

        $verificationService = app()->make(VerificationService::class, ['contact' => $contact]);
        $verificationService->markVerified();

        $role = Role::create(['name' => 'SuperAdmin', 'team_id' => 1]);
        setPermissionsTeamId(1);

        $user->assignRole($role);
    }
}
