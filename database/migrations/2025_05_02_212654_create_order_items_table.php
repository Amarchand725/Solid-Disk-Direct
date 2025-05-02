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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id')->nullable(); // Order ID
            $table->bigInteger('product_id')->nullable(); // Product ID
            $table->bigInteger('variant_id')->nullable(); // Product ID
            $table->decimal('unit_price', 10, 2)->default(0.00); // Product Price
            $table->decimal('discount', 10, 2)->nullable(); // Price per unit
            $table->integer('quantity')->nullable(); // Quantity Ordered
            $table->text('options')->nullable(); // Additional options (like size, color)
            $table->decimal('sub_total', 10, 2)->default(0.00); // Subtotal for this product
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
