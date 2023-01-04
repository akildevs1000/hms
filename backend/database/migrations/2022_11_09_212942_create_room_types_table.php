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
            $table->decimal('price', 8, 2)->default(0);


            $table->decimal('room_only_price', 8, 2)->default(0);
            $table->decimal('Break_fast_price', 8, 2)->default(0);
            $table->decimal('Break_fast_with_dinner_price', 8, 2)->default(0);
            $table->decimal('Break_fast_with_lunch_price', 8, 2)->default(0);
            $table->decimal('lunch_with_dinner_price', 8, 2)->default(0);
            $table->decimal('full_board_price', 8, 2)->default(0);


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