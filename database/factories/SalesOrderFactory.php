<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class SalesOrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'order_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'status' => 'completed',
            'so_number' => 'SO-' . now()->format('Ymd') . '-' . $this->faker->unique()->numberBetween(1000, 9999),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (SalesOrder $salesOrder) {
            $itemsCount = rand(1, 5);
            $products = Product::where('quantity', '>', 10)->inRandomOrder()->limit($itemsCount)->get();

            foreach ($products as $product) {
                $quantity = rand(1, 5);
                if ($product->quantity >= $quantity) {
                    $salesOrder->items()->create([
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $product->price,
                    ]);

                    DB::table('products')->where('id', $product->id)->decrement('quantity', $quantity);
                }
            }
        });
    }
}
