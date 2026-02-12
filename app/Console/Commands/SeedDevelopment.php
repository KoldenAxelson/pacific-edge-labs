<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

/**
 * Artisan command to seed the database with development data.
 *
 * Performs a fresh migration and seeds the database with verbose test data
 * and multiple test accounts for local development and feature testing.
 * Only runs in the local environment for safety.
 */
class SeedDevelopment extends Command
{
    protected $signature = 'db:seed-dev';
    protected $description = 'Seed database with development data';

    /**
     * Execute the command to reseed the database with development data.
     *
     * Validates that the command only runs in the local environment before proceeding.
     */
    public function handle()
    {
        if (!app()->environment('local')) {
            $this->error('This command should only run in local environment!');
            return 1;
        }

        $this->info('Seeding development database...');

        Artisan::call('migrate:fresh');
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\DatabaseSeeder']);
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\DevelopmentSeeder']);

        $this->info('✅ Development database seeded!');
        return 0;
    }
}
