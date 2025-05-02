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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id')->nullable(); // For authenticated user
            $table->string('session_id')->nullable(); // For guest cart
            $table->bigInteger('coupon_id')->nullable(); // For guest cart
            $table->enum('status', ['active', 'abandoned', 'converted'])->default('active');
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->decimal('discount_total', 10, 2)->default(0.00);
            $table->decimal('shipping_cost', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
