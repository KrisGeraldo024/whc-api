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
        Schema::create('videos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('parent_id');
            $table->foreignUuid('category');
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->string('subtitle')->nullable();
            $table->longText('description')->nullable();
            $table->longText('yt_id')->nullable();
            $table->longText('yt_url')->nullable();
            $table->longText('embed_url')->nullable();
            $table->longText('yt_title')->nullable();
            $table->longText('yt_thumbnail')->nullable();
            $table->timestamp('yt_published_date')->nullable();
            $table->longText('keyword')->nullable();
            $table->tinyInteger('featured')->default(0);
            $table->tinyInteger('enabled')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
