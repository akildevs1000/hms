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
            $table->longText('before_attachment')->change();
            $table->longText('after_attachment')->change();
            $table->longText('voice_note')->change();
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
            $table->string('before_attachment')->nullable()->change();
            $table->string('after_attachment')->nullable()->change();
            $table->string('voice_note')->nullable()->change();
        });
    }
};
