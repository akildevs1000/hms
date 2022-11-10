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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->date("date")->nullable();
            $table->string("employee_id")->nullable()->default("---");
            $table->string("shift_id")->nullable()->default("---");
            $table->string("shift_type_id")->nullable()->default("---");
            $table->string("time_table_id")->nullable()->default("---");
            $table->string("status")->nullable()->default("A");
            $table->string("in")->nullable()->default("---");
            $table->string("out")->nullable()->default("---");
            $table->string("total_hrs")->nullable()->default("---");
            $table->string("ot")->nullable()->default("---");
            $table->string("late_coming")->nullable()->default("---");
            $table->string("early_going")->nullable()->default("---");
            $table->string("device_id_in")->nullable()->default("---");
            $table->string("device_id_out")->nullable()->default("---");
            $table->string("date_in")->nullable()->default("---");
            $table->string("date_out")->nullable()->default("---");
            $table->integer("company_id")->default(0);
            $table->integer("branch_id")->default(0);
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
        Schema::dropIfExists('attendances');
    }
};