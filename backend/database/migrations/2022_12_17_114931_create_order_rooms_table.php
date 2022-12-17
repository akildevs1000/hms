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
        Schema::create('order_rooms', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->date('date');
            $table->integer('booking_id');
            $table->integer('booked_room_id');
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


            $table->string('customer_id')->nullable();
            $table->string('room_discount')->nullable();
            $table->string('after_discount')->nullable();
            $table->string('cgst')->nullable();
            $table->string('sgst')->nullable();
            $table->string('total')->nullable();
            $table->string('days')->nullable();
            $table->string('grand_total')->nullable();
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
        Schema::dropIfExists('order_rooms');
    }
};