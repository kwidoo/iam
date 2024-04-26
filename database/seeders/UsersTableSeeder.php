<?php

namespace Database\Seeders;

use App\Services\CreateUserService;
use Illuminate\Database\Seeder;
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

        CreateUserService::createUser([
            'uuid' => Str::uuid()->toString(),
            'name' => 'Oleg Pashkovsky',
            'email' => 'oleg@pashkovsky.me',
            'password' => bcrypt('test'),
            'type' => 'admin',
            'reference_id' => Str::uuid()->toString(),
        ]);;
    }
}
