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
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('purchase_order_id')->nullable();
            $table->bigInteger('product_id')->nullable();
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity')->nullable();
            $table->decimal('sub_total', 10, 2);
            $table->string('product_condition')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
