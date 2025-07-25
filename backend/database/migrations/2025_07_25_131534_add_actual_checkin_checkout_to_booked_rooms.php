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
            $table->string('actual_check_in_time')->default("---");
            $table->string('actual_check_out_time')->default("---");
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
            $table->dropColumn('actual_check_in_time');
            $table->dropColumn('actual_check_out_time');
        });
    }
};
