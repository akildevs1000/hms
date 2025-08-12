<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->string("role");
            $table->string("sender");
            $table->integer("booking_id");
            $table->integer("room_id");
            $table->integer("room_number");
            $table->string("receiption_name");


            $table->dateTime("ts");
            $table->string("text")->nullable();
            $table->string("type");
            $table->string("filename")->nullable();

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
        Schema::dropIfExists('chat_messages');
    }
};
