<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecommendationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recommendations', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->unsignedInteger('releaseYear');
            $table->string('description', 512)->nullable();
            $table->unsignedTinyInteger('type_id')->default(0);
            $table->unsignedInteger('runtime')->nullable();
            $table->unsignedInteger('tmdbRating')->nullable();
            $table->string('cover', 512)->nullable();
            $table->string('netflix_id', 64)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recommendations');
    }
}
