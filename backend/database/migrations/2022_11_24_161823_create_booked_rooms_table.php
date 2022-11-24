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
        Schema::create('booked_rooms', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('booking_id');
            $table->integer('room_id')->nullable();
            $table->string('room_no')->nullable();
            $table->string('room_type')->nullable();
            $table->string('price')->nullable();
            $table->string('bed_amount')->nullable();
            $table->string('meal')->nullable();
            $table->string('room_tax')->nullable();
            $table->string('total_with_tax')->nullable();
            $table->dateTime('check_in')->nullable();
            $table->dateTime('check_out')->nullable();
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
        Schema::dropIfExists('booked_rooms');
    }
};
