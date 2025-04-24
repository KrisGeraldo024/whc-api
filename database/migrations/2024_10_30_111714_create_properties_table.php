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
        Schema::create('properties', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('property_type');
            $table->string('name');
            $table->string('slug');
            $table->string('property_size');
            $table->tinyInteger('towers')->nullable();
            $table->string('starts_at');
            $table->string('address');
            $table->string('gmaps_link');
            $table->longText('description')->nullable();
            $table->foreignUuid('location_id');
            $table->foreignUuid('status_id');
            $table->tinyInteger('order');
            $table->tinyInteger('featured')->default(0);
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
        Schema::dropIfExists('properties');
    }
};
