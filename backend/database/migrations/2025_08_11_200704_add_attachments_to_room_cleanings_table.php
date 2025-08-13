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
        Schema::table('room_cleanings', function (Blueprint $table) {
            $table->json('attachments')->nullable(); // or after any preferred column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('room_cleanings', function (Blueprint $table) {
            $table->dropColumn('attachments');
        });
    }
};
