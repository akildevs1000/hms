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
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('meal')->nullable();
            $table->string('extra_amount')->nullable();
            $table->string('customer_type')->nullable();
            $table->string('customer_status')->nullable();
            $table->text('remark')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('meal');
            $table->dropColumn('extra_amount');
            $table->dropColumn('customer_type');
            $table->dropColumn('customer_status');
            $table->dropColumn('remark');
        });
    }
};