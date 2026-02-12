<?php

namespace Database\Seeders;

use App\Models\User;
use App\Traits\SeederHelpers;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DevelopmentSeeder extends Seeder
{
    use SeederHelpers;

    /**
     * Run development-specific seeders
     * This creates extra verbose data for local testing
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
