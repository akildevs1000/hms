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
        Schema::create('posting_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("booking_id")->default(0);
            $table->unsignedBigInteger("room_id")->default(0);
            $table->unsignedBigInteger("sub_customer_id")->default(0);
            $table->decimal('paid', 8, 2)->default(0);
            $table->decimal('balance', 8, 2)->default(0);
            
            $table->string('payment_mode')->nullable();
            $table->string('reference')->nullable();
            $table->decimal('discount', 8, 2)->default(0);
            $table->decimal('after_discount_balance', 8, 2)->default(0);
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
        Schema::dropIfExists('posting_payments');
    }
};
