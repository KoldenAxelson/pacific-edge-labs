<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call RoleSeeder first
        $this->call([
            RoleSeeder::class,
        ]);

        // Create test users
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@pacificedgelabs.test',
            'password' => bcrypt('password'),
        ]);
        $superAdmin->assignRole('super-admin');

        $customer = User::factory()->create([
            'name' => 'Test Customer',
            'email' => 'customer@pacificedgelabs.test',
            'password' => bcrypt('password'),
        ]);
        $customer->assignRole('customer');
    }
}
