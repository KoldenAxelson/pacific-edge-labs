<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

/**
 * Artisan command to seed the database with production demo data.
 *
 * Performs a fresh migration and seeds the database with demo-specific test accounts
 * and realistic sample data for demonstration and testing in staging environments.
 */
class SeedDemo extends Command
{
    protected $signature = 'db:seed-demo';
    protected $description = 'Seed database with production demo data';

    /**
     * Execute the command to reseed the database with demo data.
     *
     * Prompts for confirmation before wiping the database and running seeders.
     */
    public function handle()
    {
        $this->info('Seeding demo database...');

        if ($this->confirm('This will WIPE the database and reseed. Continue?')) {
            Artisan::call('migrate:fresh');
            Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\DatabaseSeeder']);
            Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\ProductionDemoSeeder']);

            $this->info('✅ Demo database seeded!');
        } else {
            $this->info('Cancelled.');
        }

        return 0;
    }
}
