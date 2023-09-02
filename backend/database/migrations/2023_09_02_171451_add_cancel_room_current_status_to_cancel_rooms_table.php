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
        Schema::table('cancel_rooms', function (Blueprint $table) {
            $table->integer('status_before_cancelation')->nullable();
            $table->string('status_before_cancelation_msg')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cancel_rooms', function (Blueprint $table) {
            $table->dropColumn('status_before_cancelation');
            $table->dropColumn('status_before_cancelation_msg');
        });
    }
};
