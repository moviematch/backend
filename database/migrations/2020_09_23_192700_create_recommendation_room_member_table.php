<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecommendationRoomMemberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recommendation_room_member', function (Blueprint $table) {
            $table->unsignedBigInteger('recommendation_id');
            $table->unsignedBigInteger('room_member_id');
            $table->boolean('accepted')->default(0);
            $table->boolean('shownMatch')->default(0);
            $table->primary(['recommendation_id', 'room_member_id'], 'recommendation_room_member_primary');
            $table->foreign('recommendation_id')->references('id')->on('recommendations')->onDelete('cascade');
            $table->foreign('room_member_id')->references('id')->on('room_members')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recommendation_room_member');
    }
}
