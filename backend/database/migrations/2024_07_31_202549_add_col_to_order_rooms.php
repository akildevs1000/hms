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
        Schema::table('order_rooms', function (Blueprint $table) {
            $table->integer("extra_bed_qty")->default(0);
            $table->decimal('early_check_in', 8, 2)->nullable();
            $table->decimal('late_check_out', 8, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_rooms', function (Blueprint $table) {
            $table->dropColumn("extra_bed_qty");
            $table->dropColumn("early_check_in");
            $table->dropColumn("late_check_out");
        });
    }
};
