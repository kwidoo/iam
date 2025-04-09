<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SetupSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:setup {--refresh : Whether to refresh the database before seeding}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets up the system with menus, roles, permissions, and sample users';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting system setup...');

        if ($this->option('refresh')) {
            $this->info('Refreshing database...');
            Artisan::call('migrate:refresh', ['--force' => true]);
            $this->info('Database refreshed successfully.');
        }

        $this->info('Seeding database with initial setup data...');
        Artisan::call('db:seed', ['--force' => true]);
        $this->info('Database seeded successfully.');

        $this->info('System setup complete! You can now log in with the following accounts:');
        $this->newLine();

        $this->table(
            ['Role', 'Email', 'Password'],
            [
                ['Super Admin', 'super@example.com', 'password (or as configured in your existing setup)'],
                ['Admin', 'admin@example.com', 'password'],
                ['Manager', 'manager@example.com', 'password'],
                ['Staff', 'staff@example.com', 'password'],
                ['User', 'user@example.com', 'password'],
            ]
        );

        return 0;
    }
}
