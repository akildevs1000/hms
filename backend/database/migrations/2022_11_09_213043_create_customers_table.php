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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            $table->string('customer_type')->nullable();
            $table->string('title')->nullable();
            $table->string('name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->string('nationality')->nullable();
            $table->string('image')->nullable();
            $table->string('id_card_type_id')->nullable();
            $table->string('id_card_no')->nullable();
            $table->string('car_no')->nullable();
            $table->string('no_of_adult')->nullable();
            $table->string('no_of_child')->nullable();
            $table->string('no_of_baby')->nullable();
            $table->string('address')->nullable();
            $table->integer('company_id')->default(0);

            $table->date('dob')->nullable();
            $table->string('document')->nullable();

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
        Schema::dropIfExists('customers');
    }
};
