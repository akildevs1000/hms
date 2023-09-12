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
        Schema::create('hall_booking_food', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('booking_id');
            $table->string('name');
            $table->integer('qty');
            $table->decimal('price_per_item');
            $table->decimal('total');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hall_booking_food');
    }
};
