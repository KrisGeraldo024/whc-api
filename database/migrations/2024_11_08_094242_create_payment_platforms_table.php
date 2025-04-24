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
        Schema::create('payment_platforms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('payment_method_id');  // Add this line
            $table->string('title');
            $table->string('sequence');
            $table->json('steps')->nullable();
            $table->string('buttonText');
            $table->string('buttonLink');
            $table->string('buttonActive');
            $table->timestamps();
            $table->softDeletes();
            
            // Add foreign key constraint
            $table->foreign('payment_method_id')
                  ->references('id')
                  ->on('payment_methods')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_platforms');
    }
};