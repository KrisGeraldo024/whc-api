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
        Schema::create('inquiries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('inquiry_number');
            $table->string('inquiry_type');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email_address');
            $table->string('contact_number');
            $table->string('platform')->nullable();
            $table->string('type')->nullable();
            $table->string('property_type')->nullable();
            $table->string('property_name')->nullable();
            $table->string('unit_type')->nullable();
            $table->string('job_title')->nullable();
            $table->string('partnership')->nullable();
            $table->string('company')->nullable();
            $table->string('location')->nullable();
            $table->text('message');
            $table->string('resume_path')->nullable();
            $table->string('contact_type')->nullable();
            $table->string('subject')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
