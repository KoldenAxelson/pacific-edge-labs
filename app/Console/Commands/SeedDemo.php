<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SeedDemo extends Command
{
    protected $signature = 'db:seed-demo';
    protected $description = 'Seed database with production demo data';

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
