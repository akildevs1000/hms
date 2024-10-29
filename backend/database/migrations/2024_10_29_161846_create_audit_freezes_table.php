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
        Schema::dropIfExists('audit_freezes');

        Schema::create('audit_freezes', function (Blueprint $table) {
            $table->id();
            $table->date("date");
            $table->string("check_in");
            $table->string("continue");
            $table->string("check_out");
            $table->string("booked");
            $table->string("day_use");
            $table->string("cancel");
            $table->string("breakfast");
            $table->string("ledger");
            $table->string("income");
            $table->string("expense");
            $table->string("cash_in_hand");
            $table->string("file")->nullable();
            $table->unsignedBigInteger("company_id")->default(0);
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
        Schema::dropIfExists('audit_freezes');
    }
};
