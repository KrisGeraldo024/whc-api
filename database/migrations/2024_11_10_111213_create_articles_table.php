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
        Schema::create('articles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('category_id');
            $table->string('title');
            $table->longText('content');
            $table->timestamp('date');
            $table->text('keyword');
            $table->string('slug');
            $table->tinyInteger('order');
            $table->tinyInteger('enabled')->default(1);
            $table->tinyInteger('featured')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
