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
        Schema::dropIfExists('quotation_items');

        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->date("date");
            $table->string("day");
            $table->string("room_type");
            $table->string("day_type");
            $table->integer('no_of_adult')->default(0);
            $table->integer('no_of_child')->default(0);
            $table->unsignedBigInteger('foodplan_id')->default(0);
            $table->integer('no_of_rooms')->default(0);
            $table->string('meal_name');
            $table->decimal('food_plan_price', 10, 2);
            $table->decimal('room_price', 10, 2);
            $table->decimal('room_price_with_tax', 10, 2);
            $table->decimal('early_check_in', 10, 2);
            $table->decimal('late_check_out', 10, 2);
            $table->decimal('bed_amount', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->unsignedBigInteger('quotation_id')->default(0);
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
        Schema::dropIfExists('quotation_items');
    }
};
