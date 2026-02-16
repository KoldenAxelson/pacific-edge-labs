<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);

        return [
            'category_id'        => Category::factory(),
            'sku'                => 'PEL-' . strtoupper($this->faker->lexify('???-##')),
            'name'               => ucwords($name),
            'slug'               => Str::slug($name),
            'short_description'  => $this->faker->sentence(12),
            'description'        => $this->faker->paragraphs(3, true),
            'form'               => 'Lyophilized powder',
            'concentration'      => $this->faker->randomElement(['5mg per vial', '10mg per vial', '15mg per vial']),
            'storage_conditions' => 'Store in a cool, dry environment and protect from direct light.',
            'price'              => $this->faker->randomFloat(2, 29.99, 149.99),
            'compare_price'      => null,
            'featured'           => false,
            'active'             => true,
        ];
    }

    public function onSale(): static
    {
        return $this->state(fn (array $attrs) => [
            'compare_price' => $attrs['price'] * 1.25,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(['active' => false]);
    }
}
