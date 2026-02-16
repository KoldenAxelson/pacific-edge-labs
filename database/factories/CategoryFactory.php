<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'GLP-1 & Metabolic', 'Recovery & Healing',
            'Performance & Growth', 'Cognitive & Longevity',
            'Sexual Health', 'Ancillaries',
        ]);

        return [
            'parent_id'   => null,
            'name'        => $name,
            'slug'        => Str::slug($name),
            'description' => $this->faker->paragraph(),
            'hero_title'  => null,
            'sort_order'  => $this->faker->numberBetween(1, 10),
            'active'      => true,
        ];
    }
}
