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
        // Schema::create('vendors', function (Blueprint $table) {
        //     $table->id();
        //     $table->string("title")->nullable();
        //     $table->string("first_name")->nullable();
        //     $table->string("last_name")->nullable();
        //     $table->string("company_name")->nullable();
        //     $table->string("vendor_display_name")->nullable();
        //     $table->string("email")->nullable();
        //     $table->string("work_phone")->nullable();
        //     $table->string("mobile")->nullable();
        //     $table->string("tax_number")->nullable();
        //     $table->string("address")->nullable();
        //     $table->unsignedBigInteger("vendor_category_id")->default(0);
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendors');
    }
};
