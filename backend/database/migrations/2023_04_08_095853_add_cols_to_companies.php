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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('whatsapp_instance_id')->nullable();
            $table->string('map')->nullable();
            $table->string('video')->nullable();
            $table->string('review')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('whatsapp_instance_id');
            $table->dropColumn('map');
            $table->dropColumn('video');
            $table->dropColumn('review');
        });
    }
};
