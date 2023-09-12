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
        Schema::table('room_types', function (Blueprint $table) {
            $table->decimal('hall_rent')->default(0);
            $table->decimal('projector_charges')->default(0);
            $table->decimal('cleaning_charges')->default(0);
            $table->decimal('electricity_charges')->default(0);
            $table->decimal('audio_charges')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('room_types', function (Blueprint $table) {
            $table->dropColumn('hall_rent');
            $table->dropColumn('projector_charges');
            $table->dropColumn('cleaning_charges');
            $table->dropColumn('electricity_charges');
            $table->dropColumn('audio_charges');
        });
    }
};
