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
        Schema::table('hotel_orders_food', function (Blueprint $table) {
            $table->dateTime("delivery_datetime")->nullable()->change();
            $table->integer("status")->nullable()->change();
            $table->string("reason_cancelled")->nullable()->change();
            $table->dateTime("cancelled_datetime")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hotel_orders_food', function (Blueprint $table) {
            //
        });
    }
};
