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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer("shift_type_id")->default(0);
            $table->string("working_hours")->default("---");
            $table->string('overtime_interval')->default("---");
            $table->string('on_duty_time')->default("---");
            $table->string('off_duty_time')->default("---");
            $table->string('late_time')->default("---");
            $table->string('early_time')->default("---");
            $table->string('beginning_in')->default("---");
            $table->string('ending_in')->default("---");
            $table->string('beginning_out')->default("---");
            $table->string('ending_out')->default("---");
            $table->string("absent_min_in")->default("---");
            $table->string("absent_min_out")->default("---");
            $table->json('days')->nullable();
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
        Schema::dropIfExists('shifts');
    }
};
