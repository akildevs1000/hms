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
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('price')->nullable();


            $table->string('room_only_price')->nullable();
            $table->string('Break_fast_price')->nullable();
            $table->string('Break_fast_with_dinner_price')->nullable();
            $table->string('Break_fast_with_lunch_price')->nullable();
            $table->string('lunch_with_dinner_price')->nullable();
            $table->string('full_board_price')->nullable();


            $table->string('max_person')->nullable();


            // "Breakfast",
            // "Breakfast and Dinner",
            // "Breakfast and Lunch",
            // "Full Board",
            // "Room only"


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
        Schema::dropIfExists('room_types');
    }
};