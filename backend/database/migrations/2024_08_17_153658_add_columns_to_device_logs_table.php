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
        Schema::table('device_logs', function (Blueprint $table) {
            $table->dateTime("start_datetime")->nullable();
            $table->dateTime("end_datetime")->nullable();
            $table->integer("duration_minutes")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('device_logs', function (Blueprint $table) {
            $table->dropColumn("start_datetime");
            $table->dropColumn("end_datetime");
            $table->dropColumn("duration_minutes");
        });
    }
};
