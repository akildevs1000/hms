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
        Schema::create('sub_customers', function (Blueprint $table) {
            $table->id();
            $table->string("image")->nullable();
            $table->string("title")->nullable();
            $table->string("contact_no")->nullable();
            $table->string("whatsapp")->nullable();
            $table->string("nationality")->nullable();
            $table->string("dob")->nullable();
            $table->string("first_name")->nullable();
            $table->string("last_name")->nullable();
            $table->string("address")->nullable();
            $table->string("email")->nullable();
            $table->string("customer_id")->nullable();
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
        Schema::dropIfExists('sub_customers');
    }
};
