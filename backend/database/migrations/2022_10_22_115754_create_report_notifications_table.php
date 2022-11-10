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
        Schema::create('report_notifications', function (Blueprint $table) {
            $table->id();
            $table->string("subject")->nullable();
            $table->json("body")->nullable();
            $table->string("frequency")->nullable();
            $table->string("day")->nullable();
            $table->string("date")->nullable();
            $table->string("time")->nullable();
            $table->json("reports")->nullable();
            $table->json("mediums")->nullable();
            $table->json("tos")->nullable();
            $table->json("ccs")->nullable();
            $table->json("bccs")->nullable();
            $table->integer('company_id')->default(0);
            $table->integer('branch_id')->default(0);
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
        Schema::dropIfExists('report_notifications');
    }
};
