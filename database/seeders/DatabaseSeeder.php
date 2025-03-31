<?php

namespace Database\Seeders;

use App\Contracts\Services\RegistrationService;
use App\Models\Organization;
use Database\Seeders\Menus\ContactsMenuSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use RuntimeException;
use Database\Seeders\Menus\UsersMenuSeeder;
use Database\Seeders\Menus\OrganizationsMenuSeeder;
use Database\Seeders\Menus\OrganizationUserMenuSeeder;
use Database\Seeders\Menus\InvitationsMenuSeeder;
use Database\Seeders\Menus\MicroservicesMenuSeeder;
use Database\Seeders\Menus\ProfilesMenuSeeder;
use Database\Seeders\Menus\OrganizationProfileMenuSeeder;


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



        $this->call([
            RolesAndPermissionsSeeder::class,
            UsersMenuSeeder::class,
            OrganizationsMenuSeeder::class,
            OrganizationUserMenuSeeder::class,
            InvitationsMenuSeeder::class,
            MicroservicesMenuSeeder::class,
            ProfilesMenuSeeder::class,
            OrganizationProfileMenuSeeder::class,
            ContactsMenuSeeder::class,
        ]);

        $role = Role::where('name', 'super admin')->first();

        $user->assignRole($role);

        dump($user->createToken('SuperAdmin')->accessToken);
    }
}
