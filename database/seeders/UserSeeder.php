<?php

namespace Database\Seeders;

use App\Models\User;
use App\Traits\SeederHelpers;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use SeederHelpers;

    public function run(): void
    {
        $this->info('Seeding users...');

        // Create super admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@pacificedgelabs.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole('super-admin');
        $this->info('✓ Created super admin');

        // Create regular admin
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'staff@pacificedgelabs.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');
        $this->info('✓ Created admin user');

        // Create test customers
        $customerNames = [
            'Dr. Sarah Johnson',
            'Prof. Michael Chen',
            'Dr. Emily Rodriguez',
            'Dr. James Anderson',
            'Dr. Lisa Thompson',
            'Prof. David Kim',
            'Dr. Rachel Martinez',
            'Dr. Robert Taylor',
            'Dr. Jennifer Lee',
            'Prof. Christopher Brown',
        ];

        foreach ($customerNames as $index => $name) {
            $parts = explode(' ', $name);
            $lastName = strtolower(end($parts));
            $customer = User::create([
                'name' => $name,
                'email' => $lastName . ($index + 1) . '@research.test',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
            $customer->assignRole('customer');
        }

        $this->info('✓ Created 10 test customers');
    }
}
