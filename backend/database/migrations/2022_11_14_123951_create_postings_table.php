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
        Schema::create('postings', function (Blueprint $table) {
            $table->id();

            $table->integer('booking_id')->nullable();
            $table->integer('booked_room_id')->nullable();
            $table->integer('room_id')->nullable();

            $table->integer('company_id');
            $table->string('item');
            $table->integer('qty');
            $table->decimal('amount', 8, 2);
            $table->decimal('amount_with_tax', 8, 2);
            $table->decimal('after_discount', 8, 2)->nullable();
            $table->dateTime('posting_date');

            $table->string('tax')->nullable();
            $table->string('tax_rate')->nullable();
            $table->string('sgst')->nullable();
            $table->string('cgst')->nullable();
            $table->string('tax_type')->nullable();

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
        Schema::dropIfExists('postings');
    }
};