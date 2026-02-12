<?php

namespace Database\Seeders;

use App\Traits\SeederHelpers;
use Illuminate\Database\Seeder;

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
