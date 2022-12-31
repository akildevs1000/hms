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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('booking_id');
            $table->bigInteger('customer_id');
            $table->bigInteger('company_id');
            $table->bigInteger('payment_method_id')->nullable();
            $table->decimal('debit', 8, 2)->default(0);
            $table->decimal('credit', 8, 2)->default(0);
            $table->decimal('balance', 8, 2)->default(0);
            $table->dateTime('date')->nullable();
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
        Schema::dropIfExists('transactions');
    }
};