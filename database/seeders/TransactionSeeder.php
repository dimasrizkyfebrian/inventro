<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\PurchaseOrder;
use App\Models\StockAdjustment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        PurchaseOrder::truncate();
        SalesOrder::truncate();
        StockAdjustment::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Product::query()->update(['quantity' => 50]);


        PurchaseOrder::factory(25)->create();
        SalesOrder::factory(100)->create();
        StockAdjustment::factory(20)->create();
    }
}
