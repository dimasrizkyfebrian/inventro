<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class PurchaseOrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->first()->id,
            'order_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'status' => 'completed',
            'po_number' => 'PO-' . now()->format('Ymd') . '-' . $this->faker->unique()->numberBetween(1000, 9999),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (PurchaseOrder $purchaseOrder) {
            $itemsCount = rand(1, 5);
            $products = Product::inRandomOrder()->limit($itemsCount)->get();

            foreach ($products as $product) {
                $quantity = rand(5, 20);
                $purchaseOrder->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'cost' => $product->cost ?? $this->faker->randomFloat(2, 2, 100),
                ]);

                DB::table('products')->where('id', $product->id)->increment('quantity', $quantity);
            }
        });
    }
}
