<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SeedDevelopment extends Command
{
    protected $signature = 'db:seed-dev';
    protected $description = 'Seed database with development data';

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
