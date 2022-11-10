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
        Schema::create('visas', function (Blueprint $table) {
            $table->id();
            $table->string('visa_no')->nullable();
            $table->string('place_of_issues')->nullable();
            $table->string('country')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('security_amount')->nullable();

            $table->string('labour_no')->nullable();
            $table->string('personal_no')->nullable();
            $table->date('labour_issue_date')->nullable();
            $table->date('labour_expiry_date')->nullable();

            $table->text('note')->nullable();

            $table->integer("employee_id");
            $table->integer("company_id");

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
        Schema::dropIfExists('visas');
    }
};
