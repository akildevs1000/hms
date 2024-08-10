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
            $table->decimal("cleaning", 8, 2)->default(0);
            $table->decimal("electricity", 8, 2)->default(0);
            $table->decimal("generator", 8, 2)->default(0);
            $table->decimal("audio", 8, 2)->default(0);
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
            $table->dropColumn("cleaning", 8, 2);
            $table->dropColumn("electricity", 8, 2);
            $table->dropColumn("generator", 8, 2);
            $table->dropColumn("audio", 8, 2);
        });
    }
};
