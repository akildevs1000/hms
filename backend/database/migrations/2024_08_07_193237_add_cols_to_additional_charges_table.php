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
        Schema::table('additional_charges', function (Blueprint $table) {
            $table->integer('extra_hour')->default(0);
            $table->integer('cleaning')->default(0);
            $table->integer('electricity')->default(0);
            $table->integer('generator')->default(0);
            $table->integer('audio')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('additional_charges', function (Blueprint $table) {
            $table->dropColumn('extra_hour');
            $table->dropColumn('cleaning');
            $table->dropColumn('electricity');
            $table->dropColumn('generator');
            $table->dropColumn('audio');
        });
    }
};
