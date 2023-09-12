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
        Schema::table('[hall_bookings]', function (Blueprint $table) {
            $table->decimal('hall_rent_amount')->change();
            $table->decimal('hall_rent_per_hour')->change();
            $table->decimal('hall_electricity_amount')->change();
            $table->decimal('hall_projector_amount')->change();
            $table->decimal('hall_sgst_amount')->change();
            $table->decimal('hall_cgst_amount')->change();
            $table->decimal('food_total_amount')->change();
            $table->decimal('food_cgst_amount')->change();
            $table->decimal('food_sgst_amount')->change();
            $table->decimal('inv_total_tax')->change();
            $table->decimal('inv_total_without_tax')->change();
            $table->decimal('inv_total')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
