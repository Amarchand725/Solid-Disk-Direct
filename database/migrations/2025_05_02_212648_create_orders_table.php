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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->bigInteger('customer_id')->nullable();
            $table->bigInteger('shipping_method_id')->nullable();
            $table->bigInteger('coupon_id')->nullable(); 
            $table->boolean('same_as_shipping')->default(0)->comment('0=>different address, 1=same as shipping address'); // Billing address same as shipping address
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->decimal('shipping_cost', 10, 2)->default(0.00);
            $table->decimal('tax', 10, 2)->default(0.00);
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2)->default(0.00);
            $table->string('payment_method')->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->enum('order_status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'returned'])->default('pending');
            $table->string('transaction_id')->nullable();
            $table->text('additional_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
