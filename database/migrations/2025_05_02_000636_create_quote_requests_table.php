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
        Schema::create('quote_requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('created_by')->nullable();
            $table->string('full_name')->nullable();
            $table->string('company')->nullable();
            $table->string('mpn')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('how_soon_need')->nullable();
            $table->text('message')->nullable();
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
        Schema::dropIfExists('quote_requests');
    }
};
