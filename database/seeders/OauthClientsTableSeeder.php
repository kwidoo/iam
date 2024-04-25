<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OauthClientsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('oauth_clients')->delete();
        
        \DB::table('oauth_clients')->insert(array (
            0 => 
            array (
                'id' => '9be01d61-0f3c-4606-907a-fc0049f30d3b',
                'user_id' => NULL,
                'name' => 'Main',
                'secret' => 'W0KPG2qbeH5mueoEVD5kSv9exSq3eG8tW7MohPmM',
                'provider' => 'users',
                'redirect' => 'http://localhost',
                'personal_access_client' => 0,
                'password_client' => 1,
                'revoked' => 0,
                'created_at' => '2024-04-23 10:37:58',
                'updated_at' => '2024-04-23 10:37:58',
            ),
        ));
        
        
    }
}