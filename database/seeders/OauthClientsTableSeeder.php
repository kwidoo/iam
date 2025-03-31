<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Passport;
use Laravel\Passport\ClientRepository;

class OauthClientsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('oauth_clients')->delete();
        DB::table('oauth_personal_access_clients')->delete();

        $clientRepository = app()->make(ClientRepository::class);

        // Agreement Grant Client
        $clientRepository->create(
            null,
            'Agreement Grant Client',
            '',
            null,
            false,
            false
        );

        // Property Grant Client
        $clientRepository->create(
            null,
            'Property Grant Client',
            '',
            null,
            false,
            false
        );

        // Websocket Grant Client
        $clientRepository->create(
            null,
            'Websocket Grant Client',
            '',
            null,
            false,
            false
        );

        // Main (Password Client)
        $clientRepository->create(
            null,
            'Main',
            'http://localhost',
            'users',
            false,
            true
        );

        $clientRepository->createPersonalAccessClient(
            null,
            'super-admin',
            'http://localhost'
        );
    }
}
