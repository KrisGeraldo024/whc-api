<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_id')->nullable();
            $table->string('name')->nullable();
            $table->string('size')->nullable();
            $table->string('title')->nullable();
            $table->string('alt')->nullable();
            $table->longText('caption')->nullable();
            $table->integer('sequence')->nullable();
            $table->string('path');
            $table->string('path_resized');
            $table->longText('model')->nullable();
            $table->string('category')->nullable();
            $table->string('link')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
}