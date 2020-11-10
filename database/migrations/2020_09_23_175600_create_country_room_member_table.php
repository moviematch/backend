<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountryRoomMemberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('country_room_member', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('room_member_id');
            $table->primary(['country_id', 'room_member_id'], 'country_room_member_primary');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
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
        Schema::dropIfExists('country_room_member');
    }
}
