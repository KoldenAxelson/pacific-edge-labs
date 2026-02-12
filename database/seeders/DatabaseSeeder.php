<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');

        $this->call([
            // Phase 0: Foundation
            RoleSeeder::class,
            UserSeeder::class,

            // Phase 2: Products & Inventory (placeholder for now)
            ProductSeeder::class,
            BatchSeeder::class,

            // Phase 3: CoAs (placeholder for now)
            CoaSeeder::class,

            // Phase 4: Orders (placeholder for now)
            OrderSeeder::class,
        ]);

        $this->command->info('✅ Database seeding completed!');
        $this->command->newLine();
        $this->command->info('Test accounts created:');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Super Admin', 'admin@pacificedgelabs.test', 'password'],
                ['Admin', 'staff@pacificedgelabs.test', 'password'],
                ['Customer', 'johnson1@research.test', 'password'],
            ]
        );
    }
}
