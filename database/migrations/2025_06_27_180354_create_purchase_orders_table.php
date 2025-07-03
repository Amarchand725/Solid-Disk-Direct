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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('vendor')->nullable();
            $table->string('order_number')->nullable();
            $table->string('po_number')->nullable();
            $table->date('order_date')->nullable();
            $table->decimal('sub_total', 10, 2);
            $table->decimal('tax_rate', 10, 2);
            $table->decimal('tax', 10, 2);
            $table->decimal('shipping_charges', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->string('payment_status')->nullable();
            $table->string('order_status')->nullable()->comment('pending, processing, shipped, delivered, cancelled, returned');
            $table->string('transaction_id')->nullable();
            $table->text('notes')->nullable();
            $table->string('product_condition')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('warranty_info')->nullable();
            $table->string('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
