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
            $table->integer("breakfast")->default(0);
            $table->integer("lunch")->default(0);
            $table->integer("dinner")->default(0);
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
            $table->dropColumn("breakfast");
            $table->dropColumn("lunch");
            $table->dropColumn("dinner");
        });
    }
};
