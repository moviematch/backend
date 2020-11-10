<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCastMemberRecommendationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cast_member_recommendation', function (Blueprint $table) {
            $table->unsignedBigInteger('cast_member_id');
            $table->unsignedBigInteger('recommendation_id');
            $table->primary(['cast_member_id', 'recommendation_id'], 'cast_member_recommendation_primary');
            $table->foreign('cast_member_id')->references('id')->on('cast_members')->onDelete('cascade');
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
        Schema::dropIfExists('cast_member_recommendation');
    }
}
