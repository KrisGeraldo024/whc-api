<?php

// database/migrations/2024_10_26_create_taxonomies_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('taxonomies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('type'); // To differentiate between different taxonomy types
            
            $table->json('email_recipients')->nullable(); // For inquiry types

            $table->string('location_type')->nullable(); // For office location
            $table->string('address')->nullable(); // For office location
            $table->text('map_url')->nullable(); // For office location

            $table->string('contact_number')->nullable(); // For after sales officers
            $table->string('officer_type')->nullable(); // For after sales officers
            $table->string('email')->nullable(); // For after sales officers
            // Add index for faster querying by type
            $table->index('type');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('taxonomies');
    }
};
