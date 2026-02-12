<?php

namespace Database\Seeders;

use App\Traits\SeederHelpers;
use Illuminate\Database\Seeder;

/**
 * Placeholder seeder for product data (Phase 2).
 *
 * To be implemented in Phase 2 when the Product model is created.
 * Will seed peptide and research chemical products.
 */
class ProductSeeder extends Seeder
{
    use SeederHelpers;

    public function run(): void
    {
        $this->info('ProductSeeder - Will be implemented in Phase 2');

        // Placeholder - Product model will be created in Phase 2
        // This seeder will create products like:
        // - BPC-157
        // - TB-500
        // - Ipamorelin
        // - Sermorelin
        // - etc.

        $this->info('✓ Product seeder ready for Phase 2');
    }
}
