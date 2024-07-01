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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('ref_no');
            $table->unsignedBigInteger('customer_id');
            $table->date('book_date');
            $table->date('arrival_date');
            $table->date('departure_date');
            $table->decimal('sub_total', 10, 2);
            $table->decimal('discount', 10, 2);
            $table->decimal('tax', 10, 2);
            $table->decimal('total', 10, 2);
            $table->string('bank_details');
            $table->text('terms_and_conditions');
            $table->string('status')->nullable();
            $table->string('lpo_number')->nullable();
            $table->unsignedBigInteger('quotation_id')->default(0);
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
        Schema::dropIfExists('invoices');
    }
};