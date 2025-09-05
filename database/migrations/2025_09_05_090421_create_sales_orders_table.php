<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('so_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            // Jika Anda ingin melacak customer, tambahkan customer_id di sini
            // $table->foreignId('customer_id')->constrained()->onDelete('restrict');
            $table->date('order_date');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};
