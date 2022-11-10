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
        Schema::create('time_tables', function (Blueprint $table) {
            $table->id();
            $table->string('on_duty_time');
            $table->string('off_duty_time');
            $table->string('late_time')->default("10");
            $table->string('early_time')->default("10");
            $table->string('beginning_in')->nullable();
            $table->string('ending_in')->nullable();
            $table->string('beginning_out')->nullable();
            $table->string('ending_out')->nullable();
            $table->string('count_as_workday')->default("1");
            $table->string('count_as_minute')->default("0");
            $table->string("absent_min_in")->nullable();
            $table->string("absent_min_out")->nullable();
            $table->string("break_time_start")->nullable();
            $table->string("break_time_end")->nullable();
            $table->integer('shift_id')->default(0);
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
        Schema::dropIfExists('time_tables');
    }
};
