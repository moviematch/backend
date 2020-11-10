<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountryRecommendationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('country_recommendation', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('recommendation_id');
            $table->primary(['country_id', 'recommendation_id'], 'country_recommendation_primary');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->foreign('recommendation_id')->references('id')->on('recommendations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('country_recommendation');
    }
}
