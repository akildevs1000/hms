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
        Schema::create('agents', function (Blueprint $table) {
            $table->id();

            $table->integer('booking_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->string('type')->nullable();
            $table->string('source')->nullable();
            $table->string('reference_no')->nullable();
            $table->string('booking_type')->nullable();

            $table->decimal('amount', 8, 2)->default(0);
            $table->decimal('posting_amount', 8, 2)->default(0);
            $table->decimal('agent_paid_amount', 8, 2)->default(0);

            $table->date('booking_date')->nullable();
            $table->integer('company_id')->nullable();

            $table->integer('is_paid_with_posting')->default(0);
            $table->integer('is_paid')->default(0);
            $table->integer('is_full_payment')->default(0);
            $table->string('transaction')->nullable();

            $table->date('paid_date')->nullable();
            $table->integer('payment_mode')->nullable();

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
        Schema::dropIfExists('agents');
    }
};