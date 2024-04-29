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
                'id' => '9becaa38-aaf1-4544-a06e-4bac10092c3b',
                'user_id' => NULL,
                'name' => 'Property Grant Client',
                'secret' => 'VqxFoJjRm790doZMT6Urn9ICeXx3gnPzjwpa7wEn',
                'provider' => NULL,
                'redirect' => '',
                'personal_access_client' => 0,
                'password_client' => 0,
                'revoked' => 0,
                'created_at' => '2024-04-29 16:21:44',
                'updated_at' => '2024-04-29 16:21:44',
            ),
            1 => 
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