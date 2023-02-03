<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::table('room_types', function (Blueprint $table) {
            $table->decimal('holiday_price', 8, 2)->default(0);
            $table->decimal('weekday_price', 8, 2)->default(0);
            $table->decimal('weekend_price', 8, 2)->default(0);
        });
    }


    public function down()
    {
        Schema::table('room_types', function (Blueprint $table) {
            $table->dropColumn('holiday_price');
            $table->dropColumn('weekday_price');
            $table->dropColumn('weekend_price');
        });
    }
};