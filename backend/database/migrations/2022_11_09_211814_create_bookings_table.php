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
            $table->string('rooms')->nullable();
            $table->date('booking_date')->nullable();
            $table->dateTime('check_in')->nullable();
            $table->dateTime('check_out')->nullable();
            $table->integer('booking_status')->default(1);
            $table->decimal('total_price', 8, 2)->default(0);
            $table->decimal('remaining_price', 8, 2)->default(0);
            $table->decimal('total_posting_amount', 8, 2)->default(0);
            $table->decimal('grand_remaining_price', 8, 2)->default(0)->comment('remaining price with total_posting_amount');
            $table->decimal('balance', 8, 2)->default(0);
            $table->decimal('paid_amounts', 8, 2)->default(0);
            $table->decimal('full_payment', 8, 2)->default(0);
            $table->decimal('all_room_Total_amount', 8, 2)->default(0);
            $table->decimal('advance_price', 8, 2)->default(0);
            $table->decimal('check_in_price', 8, 2)->default(0);
            $table->decimal('check_out_price', 8, 2)->default(0);

            $table->integer('payment_status')->default(0);
            $table->integer('payment_mode_id')->default(1);
            $table->string('total_days')->nullable();
            $table->string('sub_total')->nullable();
            $table->string('sales_tax')->nullable();
            $table->string('discount')->nullable();
            $table->string('after_discount')->nullable();
            $table->string('document')->nullable();
            $table->text('request')->nullable();
            $table->text('customer_status')->nullable();
            $table->text('purpose')->nullable();
            $table->integer('company_id')->default(0);

            $table->string('customer_type')->nullable();
            $table->text('remark')->nullable();
            $table->decimal('total_extra', 8, 2)->nullable();

            $table->string('reference_no')->nullable();
            $table->string('paid_by')->nullable(); // paid by hotel => 1,  paid by agent => 2,

            $table->text('reason')->nullable();
            $table->integer('user_id')->nullable();
            $table->dateTime('cancel_date')->nullable();

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
