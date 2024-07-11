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
        Schema::create('expense_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("vendor_id")->default(0);
            $table->string("payment_number")->nullable();
            $table->date("payment_date")->nullable();
            $table->string("attachment")->nullable();
            $table->string("note")->nullable();
            $table->string("payment_mode")->nullable();
            $table->string("payment_mode_ref")->nullable();
            $table->unsignedBigInteger("admin_expense_id")->default(0);
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
        Schema::dropIfExists('expense_payments');
    }
};
