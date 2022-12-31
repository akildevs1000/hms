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
            $table->decimal('price', 8, 2)->default(0);
            $table->decimal('bed_amount', 8, 2)->default(0);
            $table->string('meal')->nullable();
            $table->decimal('room_tax', 8, 2)->default(0);
            $table->decimal('total_with_tax', 8, 2)->default(0);
            $table->dateTime('check_in')->nullable();
            $table->dateTime('check_out')->nullable();

            $table->string('customer_id')->nullable();
            $table->decimal('room_discount', 8, 2)->default(0);
            $table->decimal('after_discount', 8, 2)->default(0);
            $table->decimal('cgst', 8, 2)->default(0);
            $table->decimal('sgst', 8, 2)->default(0);
            $table->decimal('total', 8, 2)->default(0);
            $table->string('days')->nullable();
            $table->decimal('grand_total', 8, 2)->default(0);

            $table->string('no_of_adult')->nullable();
            $table->string('no_of_child')->nullable();
            $table->string('no_of_baby')->nullable();

            $table->string('booking_status')->default(1);


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