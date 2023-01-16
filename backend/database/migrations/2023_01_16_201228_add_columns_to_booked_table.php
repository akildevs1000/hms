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
        Schema::table('booked_rooms', function (Blueprint $table) {
            $table->string('discount_reason')->nullable();
        });
        Schema::table('cancel_rooms', function (Blueprint $table) {
            $table->string('discount_reason')->nullable();
        });
        Schema::table('order_rooms', function (Blueprint $table) {
            $table->string('discount_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booked_rooms', function (Blueprint $table) {
            $table->dropColumn('discount_reason');
        });
        Schema::table('cancel_rooms', function (Blueprint $table) {
            $table->dropColumn('discount_reason');
        });
        Schema::table('order_rooms', function (Blueprint $table) {
            $table->dropColumn('discount_reason');
        });
    }
};