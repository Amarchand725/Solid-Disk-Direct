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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // To associate the action with a user
            $table->string('action'); // e.g., "create", "update", "delete", etc.
            $table->string('model'); // The model name or table affected
            $table->unsignedBigInteger('model_id')->nullable(); // The ID of the affected record
            $table->json('changed_fields')->nullable(); // For storing changed fields in an update
            $table->ipAddress('ip_address')->nullable(); // To store the IP address
            $table->text('description')->nullable(); // Additional details
            $table->text('extra_details')->nullable(); // Additional details
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
