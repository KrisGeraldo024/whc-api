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
        Schema::create('careers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('slug');
            $table->string('job_type');
            $table->foreignUuid('location_id')->nullable();
            $table->longText('description')->nullable();
            $table->longText('qualifications')->nullable();
            $table->foreignUuid('employment_type_id')->nullable();
            $table->timestamp('date');
            $table->tinyInteger('enabled')->default(1);
            $table->tinyInteger('order');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('careers');
    }
};
