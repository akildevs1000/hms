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
        Schema::table('hall_bookings', function (Blueprint $table) {
            $table->decimal('hall_audio_system')->default(0);
            $table->decimal('hall_cleaning_charges')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hall_bookings', function (Blueprint $table) {
            //
        });
    }
};
