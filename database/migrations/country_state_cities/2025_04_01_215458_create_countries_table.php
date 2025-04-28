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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('iso2', 2)->unique(); // Two-letter country code (US, IN, CA)
            $table->string('iso3', 3)->unique(); // Three-letter country code (USA, IND, CAN)
            $table->string('phone_code')->nullable(); // Country calling code (+1, +91)
            $table->string('currency')->nullable(); // Country calling code (+1, +91)
            $table->string('currency_name')->nullable(); // Country calling code (+1, +91)
            $table->string('currency_symbol')->nullable(); // Country calling code (+1, +91)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
