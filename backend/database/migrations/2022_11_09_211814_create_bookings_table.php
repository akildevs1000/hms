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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id')->nullable();
            $table->string('type')->nullable();
            $table->string('source')->nullable();
            $table->string('agent_name')->nullable();
            $table->integer('room_id')->nullable();
            $table->date('booking_date')->nullable();
            $table->dateTime('check_in')->nullable();
            $table->dateTime('check_out')->nullable();
            $table->string('total_price')->nullable();
            $table->string('remaining_price')->nullable();
            $table->integer('payment_status')->nullable();
            $table->integer('payment_mode_id')->default(1);
            $table->string('total_days')->nullable();
            $table->string('sub_total')->nullable();
            $table->string('sales_tax')->nullable();
            $table->string('discount')->nullable();
            $table->string('after_discount')->nullable();
            $table->string('advance_price')->nullable();
            $table->string('check_in_price')->nullable();
            $table->string('check_out_price')->nullable();
            $table->string('document')->nullable();
            $table->text('request')->nullable();
            $table->integer('company_id')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};