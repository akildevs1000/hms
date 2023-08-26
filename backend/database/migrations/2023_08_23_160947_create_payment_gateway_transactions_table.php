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
        Schema::create('payment_gateway_transactions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('company_id');
            $table->integer('paytm_orderId');
            $table->string('paytm_amount');
            $table->string('paytm_tokenType');
            $table->string('paytm_token');
            $table->string('paytm_status');
            $table->string('customer_name');
            $table->string('booking_id');
            $table->json('request_json');
            $table->json('response_json');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_gateway_transactions');
    }
};
