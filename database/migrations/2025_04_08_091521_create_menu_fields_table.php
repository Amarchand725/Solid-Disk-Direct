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
        Schema::create('menu_fields', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('menu_id')->nullable();
            $table->string('name')->nullable();          // e.g. 'title', 'image', 'status'
            $table->string('data_type')->nullable();          // e.g. 'text', 'textarea', 'select', 'file'
            $table->string('input_type')->nullable();          // e.g. 'text', 'textarea', 'select', 'file'
            $table->string('label')->nullable();         // e.g. 'Title', 'Status'
            $table->string('placeholder')->nullable();
            $table->boolean('required')->default(false);
            $table->boolean('index_visible')->default(true);
            $table->boolean('create_visible')->default(true);
            $table->boolean('edit_visible')->default(true);
            $table->boolean('show_visible')->default(true);
            $table->json('extra')->nullable(); // dynamic attributes like accept, data-id, validation
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_fields');
    }
};
