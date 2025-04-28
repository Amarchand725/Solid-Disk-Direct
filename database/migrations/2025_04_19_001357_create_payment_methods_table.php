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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('created_by')->nullable();
            $table->string('logo')->nullable();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->string('type')->nullable();
            $table->text('mode')->nullable();
            $table->text('test_public_key')->nullable();
            $table->text('test_secret_key')->nullable();
            $table->text('live_public_key')->nullable();
            $table->text('live_secret_key')->nullable();
            $table->text('instructions')->nullable();
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
        Schema::dropIfExists('payment_methods');
    }
};
