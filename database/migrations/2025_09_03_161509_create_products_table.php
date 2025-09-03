<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique(); // Stock Keeping Unit
            $table->text('description')->nullable();
            $table->integer('quantity')->default(0);
            $table->decimal('price', 15, 2); // Selling price
            $table->decimal('cost', 15, 2)->nullable(); // Buying price
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->integer('reorder_point')->default(10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
