<?php

namespace Database\Seeders;

use App\Models\User;
use App\Traits\SeederHelpers;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Development-specific database seeder for local testing.
 *
 * Creates additional test users and verbose sample data for local development,
 * feature testing, and stress testing. Only runs in the local environment.
 */
class DevelopmentSeeder extends Seeder
{
    use SeederHelpers;

    /**
     * Run development-specific seeders.
     * This creates extra verbose data for local testing.
     *
     * @return void
     */
    public function run(): void
    {
        if (!app()->environment('local')) {
            $this->command->error('DevelopmentSeeder should only run in local environment!');
            return;
        }

        $this->info('Running development seeders...');

        // Create extra test users
        for ($i = 1; $i <= 50; $i++) {
            User::create([
                'name' => "Test Customer {$i}",
                'email' => "customer{$i}@test.local",
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ])->assignRole('customer');
        }

        $this->info('✓ Created 50 additional test customers');

        // Future: Create lots of orders, products, etc. for stress testing
    }
}
