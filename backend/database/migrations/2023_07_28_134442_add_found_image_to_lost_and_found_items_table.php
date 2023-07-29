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
        Schema::table('lost_and_found_items', function (Blueprint $table) {
            $table->string('found_image')->nullable();
            $table->string('recovered_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lost_and_found_items', function (Blueprint $table) {
            $table->dropColumn('found_image');
            $table->dropColumn('recovered_image');
        });
    }
};
