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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('created_by')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->string('sku')->nullable()->comment('Stock Keeping Unit');
            $table->string('mpn')->nullable()->comment('Manufacturer Part Number');
            $table->bigInteger('brand')->nullable();
            $table->bigInteger('category')->nullable();
            $table->bigInteger('stock_quantity')->nullable();
            $table->integer('min_quantity')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('full_description')->nullable();
            $table->decimal('unit_price')->nullable();
            $table->decimal('discount_price')->nullable();
            $table->boolean('is_featured')->nullable();
            $table->boolean('is_refundable')->nullable();
            $table->string('unit')->nullable();
            $table->string('tax_type')->nullable();
            $table->string('tax_mode')->nullable();
            $table->string('discount_type')->nullable();
            $table->string('condition')->nullable();
            $table->text('tags')->nullable();
            $table->boolean('status')->default(true);
            $table->string('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
