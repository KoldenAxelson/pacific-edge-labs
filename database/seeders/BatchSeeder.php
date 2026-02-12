<?php

namespace Database\Seeders;

use App\Traits\SeederHelpers;
use Illuminate\Database\Seeder;

class BatchSeeder extends Seeder
{
    use SeederHelpers;

    public function run(): void
    {
        $this->info('BatchSeeder - Will be implemented in Phase 2');

        // Placeholder - Batch model will be created in Phase 2
        // This seeder will create batches for products with:
        // - Batch numbers
        // - Quantities
        // - Manufacturing dates
        // - Expiry dates

        $this->info('✓ Batch seeder ready for Phase 2');
    }
}
