<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Kwidoo\Contacts\Contracts\ContactService;
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

        $user = $model::create();
        $contactService = app()->make(ContactService::class, ['model' => $user]);

        $uuid = $contactService->create('email', 'admin@example.com');
    }
}
