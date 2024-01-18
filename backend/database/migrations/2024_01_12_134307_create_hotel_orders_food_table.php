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
        Schema::create('hotel_orders_food', function (Blueprint $table) {
            $table->id();
            $table->integer("company_id");
            $table->integer("booking_id");
            $table->integer("room_id");
            $table->integer("food_item_id");
            $table->decimal("food_price", 8, 2)->default(0);
            $table->decimal("food_sgst", 8, 2)->default(0);
            $table->decimal("food_cgst", 8, 2)->default(0);
            $table->decimal("food_total", 8, 2)->default(0);
            $table->dateTime("delivery_datetime");
            $table->integer("status");
            $table->string("reason_cancelled");
            $table->dateTime("cancelled_datetime");

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
        Schema::dropIfExists('hotel_orders_food');
    }
};
