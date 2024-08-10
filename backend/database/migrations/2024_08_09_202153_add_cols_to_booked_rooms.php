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
            $table->integer("hall_min_hours")->default(0);
            $table->decimal("projector", 8, 2)->default(0);
            $table->decimal("extra_hours", 8, 2)->default(0);
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
            $table->dropColumn('hall_min_hours');
            $table->dropColumn('projector');
            $table->dropColumn('extra_hours');
        });
    }
};
