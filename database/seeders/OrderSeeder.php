<?php

namespace Database\Seeders;

use App\Traits\SeederHelpers;
use Illuminate\Database\Seeder;

/**
 * Placeholder seeder for order data (Phase 4).
 *
 * To be implemented in Phase 4 when the Order model is created.
 * Will seed realistic orders with multiple products, various statuses, payments, and history.
 */
class OrderSeeder extends Seeder
{
    use SeederHelpers;

    public function run(): void
    {
        $this->info('OrderSeeder - Will be implemented in Phase 4');

        // Placeholder - Order model will be created in Phase 4
        // This seeder will create realistic orders with:
        // - Multiple products per order
        // - Various statuses (pending, processing, shipped, delivered)
        // - Payment transactions
        // - Order history over past 6 months

        $this->info('✓ Order seeder ready for Phase 4');
    }
}
