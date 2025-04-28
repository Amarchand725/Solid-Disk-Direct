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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('ABC');
            $table->string('logo')->nullable();
            $table->string('black_logo')->nullable();
            $table->string('slip_stamp')->nullable();
            $table->string('admin_signature')->nullable();
            $table->string('support_email')->nullable();
            $table->string('sale_email')->nullable();
            $table->string('day_range')->nullable();
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
            $table->string('timezone')->nullable();
            $table->string('phone_number')->nullable();
            $table->text('website_url')->nullable();
            $table->string('favicon')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('area')->nullable();
            $table->string('zip_code')->nullable();
            $table->text('address')->nullable();
            $table->string('currency_symbol')->nullable();
            $table->text('facebook_link')->nullable();
            $table->text('instagram_link')->nullable();
            $table->text('linked_in_link')->nullable();
            $table->text('twitter_link')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
