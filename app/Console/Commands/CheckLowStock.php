<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class CheckLowStock extends Command
{
    protected $signature = 'app:check-low-stock';
    protected $description = 'Check for products with low stock and notify admins';

    public function handle()
    {
        $this->info('Checking for low stock products...');

        $lowStockProducts = Product::whereColumn('quantity', '<=', 'reorder_point')->get();

        if ($lowStockProducts->isEmpty()) {
            $this->info('No low stock products found.');
            return;
        }

        $usersToNotify = User::all();

        foreach ($lowStockProducts as $product) {
            Notification::send($usersToNotify, new \App\Notifications\LowStockProductNotification($product));
            $this->line('Notification sent for product: ' . $product->name);
        }

        $this->info('Low stock check complete.');
    }
}
