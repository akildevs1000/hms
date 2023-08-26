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
        Schema::table('order_rooms', function (Blueprint $table) {
            $table->decimal('inv_room_listing_price')->default(0);
            $table->decimal('inv_room_discount')->default(0);
            $table->decimal('inv_room_price_after_discount')->default(0);
            $table->decimal('inv_room_cgst')->default(0);
            $table->decimal('inv_room_sgst')->default(0);

            $table->decimal('inv_food_listing_price')->default(0);
            $table->decimal('inv_food_sgst')->default(0);
            $table->decimal('inv_food_cgst')->default(0);

            $table->decimal('inv_room_food_gst_grand_total')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_rooms', function (Blueprint $table) {
            $table->dropColumn('inv_room_listing_price');
            $table->dropColumn('inv_room_discount');
            $table->dropColumn('inv_room_price_after_discount');
            $table->dropColumn('inv_room_cgst');
            $table->dropColumn('inv_room_sgst');
            $table->dropColumn('inv_food_listing_price');
            $table->dropColumn('inv_food_sgst');
            $table->dropColumn('inv_food_cgst');

            $table->dropColumn('inv_room_food_gst_grand_total');
        });
    }
};
