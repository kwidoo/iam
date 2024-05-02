<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Contracts\CreateUserService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('users')->delete();

        $userUuid = Str::uuid()->toString();
        $userService = app(CreateUserService::class);
        $userService([
            'user_uuid' => $userUuid,
            'name' => 'Oleg Pashkovsky',
            'email' => 'oleg@pashkovsky.me',
            'password' => bcrypt('test'),
            'type' => 'admin',
            'reference_id' => Str::uuid()->toString(),
        ]);


        Role::create([
            'name' => 'admin',
            'guard_name' => 'api',
        ]);

        $role = Role::create([
            'name' => 'landlord',
            'guard_name' => 'api',
        ]);

        Role::create([
            'name' => 'tenant',
            'guard_name' => 'api',
        ]);

        $permission = Permission::create([
            'name' => 'create property',
            'guard_name' => 'api',
        ]);

        $role->givePermissionTo($permission);

        $permission = Permission::create([
            'name' => 'remove property',
            'guard_name' => 'api',
        ]);

        $role->givePermissionTo($permission);

        DB::insert('insert into model_has_roles (role_uuid, model_type, model_uuid) values (?, ?, ?)', [$role->uuid, 'user', $userUuid]);
    }
}
