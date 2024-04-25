<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

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

        User::createUser([
            'name' => 'Oleg Pashkovsky',
            'email' => 'oleg@pashkovsky.me',
            'password' => bcrypt('test'),
        ]);
    }
}
