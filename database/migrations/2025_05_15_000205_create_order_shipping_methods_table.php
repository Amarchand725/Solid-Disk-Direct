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
        Schema::create('order_shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('cart_id')->nullable()->comment('before order place store cart id');
            $table->bigInteger('order_id')->nullable()->comment('after order placed store order id');
            $table->string('service_name')->nullable();
            $table->string('service_type')->nullable();
            $table->string('total_net_charges')->nullable();
            $table->string('currency')->nullable();
            $table->string('total_base_charges')->nullable();
            $table->string('fuel_surcharges')->nullable();
            $table->string('delivery_surcharges')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_shipping_methods');
    }
};
