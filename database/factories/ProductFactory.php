<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => ucwords($this->faker->words(3, true)),
            'sku' => $this->faker->unique()->ean8,
            'description' => $this->faker->paragraph,
            'quantity' => $this->faker->numberBetween(10, 100),
            'price' => $this->faker->randomFloat(2, 5, 200),
            'cost' => $this->faker->randomFloat(2, 2, 100),
            'category_id' => Category::inRandomOrder()->first()->id,
            'reorder_point' => 10,
        ];
    }
}
