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
            $table->decimal('single_day_extra_amount', 8, 2)->default(0);
            $table->decimal('single_day_discount', 8, 2)->default(0);
            $table->decimal('miscellaneous_total', 8, 2)->default(0);
            $table->decimal('miscellaneous_total_without_tax', 8, 2)->default(0);
            $table->decimal('miscellaneous_tax', 8, 2)->default(0);
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
            $table->dropColumn("single_day_extra_amount");
            $table->dropColumn("single_day_discount");
            $table->dropColumn("miscellaneous_total");
            $table->dropColumn("miscellaneous_total_without_tax");
            $table->dropColumn("miscellaneous_tax");
        });
    }
};
