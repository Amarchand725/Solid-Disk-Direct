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
        Schema::create('recent_view_products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('created_by')->nullable();
            $table->string('product')->nullable();
            $table->bigInteger('customer')->nullable();
            $table->string('guest')->nullable();
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
        Schema::dropIfExists('recent_view_products');
    }
};
