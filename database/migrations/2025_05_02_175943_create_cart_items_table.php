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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('cart_id')->nullable(); // Link to the cart
            $table->bigInteger('product_id')->nullable(); // Link to product table
            $table->bigInteger('variant_id')->nullable(); // Link to product table
            $table->integer('quantity')->nullable(); // Quantity
            $table->decimal('unit_price', 10, 2)->nullable(); // Price at time of adding to cart
            $table->decimal('discount', 10, 2)->nullable(); // Price per unit
            $table->text('options')->nullable(); // Additional options (like size, color)
            $table->decimal('sub_total', 10, 2)->default(0); // Sub Total cart value
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
