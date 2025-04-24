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
        Schema::create('units', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('parent_id')->references('id')->on('properties')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->string('location');
            $table->string('starts_at');
            $table->string('unit_type');
            $table->string('floor_area');
            $table->string('lot_area')->nullable();
            $table->tinyInteger('bedroom')->nullable();
            $table->tinyInteger('t_and_b')->nullable(); //toilet and bath
            $table->tinyInteger('storeys')->nullable();
            $table->tinyInteger('powder_room')->nullable();
            $table->tinyInteger('order');
            $table->tinyInteger('enabled')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
