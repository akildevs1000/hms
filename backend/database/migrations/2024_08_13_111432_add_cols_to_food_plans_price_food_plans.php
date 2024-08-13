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
        Schema::table('food_plans', function (Blueprint $table) {
            $table->boolean("breakfast")->default(true);
            $table->boolean("lunch")->default(true);
            $table->boolean("dinner")->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('food_plans', function (Blueprint $table) {
            $table->dropColumn("breakfast");
            $table->dropColumn("lunch");
            $table->dropColumn("dinner");
        });
    }
};
