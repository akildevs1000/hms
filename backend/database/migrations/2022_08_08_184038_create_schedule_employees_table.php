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
        Schema::create('schedule_employees', function (Blueprint $table) {
            $table->id();
            $table->integer('shift_id')->default(0);
            $table->boolean("isOverTime")->default(0);
            $table->string("employee_id")->default(0);
            $table->integer("shift_type_id")->default(0);
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
        Schema::dropIfExists('schedule_employees');
    }
};
