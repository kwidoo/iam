<?php

namespace Database\Seeders;

use App\Contracts\Services\RegistrationService;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
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
        $this->call(MenuItemsTableSeeder::class);

        $provider = config('auth.guards.api.provider');
        $model = config('auth.providers.' . $provider . '.model');
        if (!$model) {
            throw new RuntimeException('Unable to determine contact model from configuration.');
        }
        Model::withoutEvents(function () use ($model) {
            Organization::create(['name' => 'main', 'slug' => 'main']);
        });


        $user = app()->make(RegistrationService::class)->registerNewUser([
            'value' => 'admin@example.com',
            'type' => 'email',
            'method' => 'email',
            'password' => bcrypt('admin123'),
            'organization' => Organization::first(),
            'fname' => 'Admin',
            'lname' => 'Admin',
            'dob' => '1978-04-06',
            'gender' => 'm',
        ]);

        $role = Role::create(['name' => 'SuperAdmin', 'team_id' => 1]);
        setPermissionsTeamId(1);

        $user->assignRole($role);
    }
}
