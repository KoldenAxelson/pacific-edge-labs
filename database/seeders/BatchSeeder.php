<?php

namespace Database\Seeders;

use App\Traits\SeederHelpers;
use Illuminate\Database\Seeder;

/**
 * Placeholder seeder for product batch data (Phase 2).
 *
 * To be implemented in Phase 2 when the Batch model is created.
 * Will seed batch numbers, quantities, manufacturing and expiry dates.
 */
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
