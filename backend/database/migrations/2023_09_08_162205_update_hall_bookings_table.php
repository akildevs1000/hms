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
