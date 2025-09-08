<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class StockAdjustmentFactory extends Factory
{
    public function definition(): array
    {
        $type = $this->faker->randomElement(['addition', 'subtraction']);
        $product = Product::inRandomOrder()->first();
        $quantity = rand(1, 5);

        if ($type === 'subtraction' && $product->quantity < $quantity) {
            $type = 'addition';
        }

        return [
            'product_id' => $product->id,
            'user_id' => User::inRandomOrder()->first()->id,
            'type' => $type,
            'quantity' => $quantity,
            'reason' => $this->faker->sentence(4),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (\App\Models\StockAdjustment $adjustment) {
            if ($adjustment->type === 'addition') {
                DB::table('products')->where('id', $adjustment->product_id)->increment('quantity', $adjustment->quantity);
            } else {
                DB::table('products')->where('id', $adjustment->product_id)->decrement('quantity', $adjustment->quantity);
            }
        });
    }
}
