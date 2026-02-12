<?php

namespace Database\Seeders;

use App\Models\User;
use App\Traits\SeederHelpers;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Production demo database seeder for staging deployments.
 *
 * Creates realistic but limited demo data for demonstration and testing in staging environments.
 * Includes a demo admin account and a few realistic demo customers for presentation purposes.
 */
class ProductionDemoSeeder extends Seeder
{
    use SeederHelpers;

    /**
     * Run production demo seeder.
     * This creates realistic but limited data for demo deployment.
     *
     * @return void
     */
    public function run(): void
    {
        $this->info('Running production demo seeder...');

        // Only create demo admin if it doesn't exist
        if (!User::where('email', 'demo@pacificedgelabs.test')->exists()) {
            $demoAdmin = User::create([
                'name' => 'Demo Admin',
                'email' => 'demo@pacificedgelabs.test',
                'password' => Hash::make(env('DEMO_ADMIN_PASSWORD', 'change-this-password')),
                'email_verified_at' => now(),
            ]);
            $demoAdmin->assignRole('super-admin');

            $this->info('✓ Created demo admin account');
        } else {
            $this->info('✓ Demo admin already exists');
        }

        // Create a few realistic demo customers
        $demoCustomers = [
            ['name' => 'Dr. Alex Morgan', 'email' => 'alex.morgan@university.edu'],
            ['name' => 'Dr. Sam Rivera', 'email' => 'sam.rivera@research.org'],
            ['name' => 'Dr. Jordan Lee', 'email' => 'jordan.lee@biotech.com'],
        ];

        foreach ($demoCustomers as $customerData) {
            if (!User::where('email', $customerData['email'])->exists()) {
                User::create([
                    'name' => $customerData['name'],
                    'email' => $customerData['email'],
                    'password' => Hash::make('demo-password'),
                    'email_verified_at' => now(),
                ])->assignRole('customer');
            }
        }

        $this->info('✓ Created 3 demo customers');
    }
}
