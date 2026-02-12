<?php

namespace Database\Seeders;

use App\Traits\SeederHelpers;
use Illuminate\Database\Seeder;

/**
 * Placeholder seeder for Certificate of Analysis (CoA) data (Phase 3).
 *
 * To be implemented in Phase 3 when the CoA model is created.
 * Will seed sample PDF CoAs, upload to S3, and link to product batches.
 */
class CoaSeeder extends Seeder
{
    use SeederHelpers;

    public function run(): void
    {
        $this->info('CoaSeeder - Will be implemented in Phase 3');

        // Placeholder - CoA model will be created in Phase 3
        // This seeder will:
        // - Generate sample PDF CoAs
        // - Upload to S3
        // - Link to batches

        $this->info('✓ CoA seeder ready for Phase 3');
    }
}
