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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('phone_relative_number')->nullable();
            $table->string('whatsapp_relative_number')->nullable();
            $table->string('employee_id')->nullable();
            $table->date('joining_date')->nullable();
            $table->integer('designation_id')->nullable();
            $table->integer('department_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer("role_id")->nullable();
            $table->integer("sub_department_id")->default(0);
            $table->integer('overtime')->nullable();
            $table->integer('mobile_application')->nullable();
            $table->string('relation')->nullable();

            $table->string('file_no')->nullable();
            $table->string('type')->nullable();
            $table->string('title')->nullable();
            $table->string('grade')->nullable();
            $table->string('work_site')->nullable();
            $table->integer('status')->nullable();
            $table->integer("employee_role_id")->default(0);

            //local contact
            $table->string('local_address')->nullable();
            $table->string('local_tel')->nullable();
            $table->string('local_mobile')->nullable();
            $table->string('local_fax')->nullable();
            $table->string('local_city')->nullable();
            $table->string('local_country')->nullable();
            $table->string('local_email')->nullable();
            $table->string('local_residence_no')->nullable();

            //home country contact
            $table->string('home_address')->nullable();
            $table->string('home_tel')->nullable();
            $table->string('home_mobile')->nullable();
            $table->string('home_fax')->nullable();
            $table->string('home_city')->nullable();
            $table->string('home_state')->nullable();
            $table->string('home_country')->nullable();
            $table->string('home_email')->nullable();

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
        Schema::dropIfExists('employees');
    }
};
