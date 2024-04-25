<?php

namespace Database\Seeders;

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

        \DB::table('users')->insert(array(
            0 =>
            array(
                'uuid' => '01hw58c07byf7k9p5dc32sesde',
                'password' => '$2y$12$6xbMeaFR8EIWCv.xKCHKJuXPgkJ51iJYQrPo9lAtlgs5Q67PrubOm',  // test
                'created_at' => '2024-04-23 10:35:05',
                'updated_at' => '2024-04-23 10:35:05',
            ),
        ));
    }
}
