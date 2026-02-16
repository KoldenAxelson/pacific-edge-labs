<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'sort_order'  => 1,
                'name'        => 'GLP-1 & Metabolic',
                'slug'        => 'glp-1-metabolic',
                'hero_title'  => 'GLP-1 & Metabolic Research Compounds',
                'description' => 'GLP-1 receptor agonists and metabolic research peptides studied for their role in appetite regulation, glucose homeostasis, and energy metabolism. These compounds are among the most actively researched in modern metabolic science.',
            ],
            [
                'sort_order'  => 2,
                'name'        => 'Recovery & Healing',
                'slug'        => 'recovery-healing',
                'hero_title'  => 'Recovery & Tissue Healing Research Peptides',
                'description' => 'Peptides studied for tissue repair, wound healing, and inflammatory pathway modulation. Used in research exploring musculoskeletal recovery, gut integrity, and cellular regeneration.',
            ],
            [
                'sort_order'  => 3,
                'name'        => 'Performance & Growth',
                'slug'        => 'performance-growth',
                'hero_title'  => 'Performance & Growth Hormone Research',
                'description' => 'Growth hormone secretagogues and related compounds studied for their role in GH-axis signaling, lean body composition research, and endocrine pathway modeling.',
            ],
            [
                'sort_order'  => 4,
                'name'        => 'Cognitive & Longevity',
                'slug'        => 'cognitive-longevity',
                'hero_title'  => 'Cognitive Function & Longevity Research',
                'description' => 'Nootropic, neuroprotective, and longevity-related research compounds studied for their effects on neurological signaling, cellular aging, telomerase activity, and cognitive performance.',
            ],
            [
                'sort_order'  => 5,
                'name'        => 'Sexual Health',
                'slug'        => 'sexual-health',
                'hero_title'  => 'Sexual Health & Melanocortin Research',
                'description' => 'Melanocortin receptor agonists and related compounds studied for their roles in reproductive signaling, pigmentation pathways, and neuroendocrine modulation.',
            ],
            [
                'sort_order'  => 6,
                'name'        => 'Ancillaries',
                'slug'        => 'ancillaries',
                'hero_title'  => 'Research Ancillaries & Reconstitution Supplies',
                'description' => 'Supporting materials for peptide research, including pharmaceutical-grade bacteriostatic water for reconstitution.',
            ],
        ];

        foreach ($categories as $data) {
            Category::updateOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, ['parent_id' => null, 'active' => true])
            );
        }
    }
}
