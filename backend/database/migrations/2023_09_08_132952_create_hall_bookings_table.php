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
        Schema::create('hall_bookings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('customer_id');
            $table->integer('room_id');
            $table->integer('room_number');
            $table->integer('reference_no');
            $table->integer('reservation_no');
            $table->integer('company_id');

            $table->date('booking_date');
            $table->integer('booking_status')->default(0);
            $table->string('customer_request')->nullable();
            $table->string('source_type');
            $table->string('customer_type');
            $table->string('id_card_type');
            $table->string('id_card_no');
            $table->date('id_exp_date');
            $table->integer('paid_by');
            $table->string('purpose');
            $table->string('cancel_reason');
            $table->integer('user_id');
            $table->date('cancel_date');
            $table->dateTime('cancel_date_time');

            $table->date('event_date');
            $table->integer('event_start_time');
            $table->integer('event_end_time');
            $table->string('event_type');
            $table->string('event_pax');
            $table->string('event_special_setup');
            $table->string('event_audio_system');
            $table->string('event_projector');
            $table->string('event_stage_decoration')->nullable();
            $table->integer('hall_rent_amount')->default(0);
            $table->integer('hall_total_hours')->default(0);
            $table->integer('hall_rent_per_hour')->default(0);
            $table->integer('hall_electricity_amount')->default(0);
            $table->integer('hall_projector_amount')->default(0);
            $table->integer('hall_tax_per')->default(0);
            $table->integer('hall_sgst_amount')->default(0);
            $table->integer('hall_cgst_amount')->default(0);
            $table->integer('food_total_amount')->default(0);
            $table->integer('food_tax_per')->default(0);
            $table->integer('food_cgst_amount')->default(0);
            $table->double('food_sgst_amount')->default(0);
            $table->double('inv_total_tax')->default(0);
            $table->double('inv_total_without_tax')->default(0);
            $table->double('inv_total')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hall_bookings');
    }
};
