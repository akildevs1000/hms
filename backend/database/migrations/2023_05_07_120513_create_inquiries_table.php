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
        Schema::create('inquiries', function (Blueprint $table) {
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
            $table->dateTime('check_in')->nullable();
            $table->dateTime('check_out')->nullable();
            $table->integer('days')->nullable();
            $table->string('rooms_type')->nullable();
            $table->integer('number_of_rooms')->nullable();
            $table->string('rooms')->nullable();
            $table->string('purpose')->nullable();
            $table->string('customer_request')->nullable();
            $table->string('remark')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->integer('company_id')->default(0);
            $table->date('dob')->nullable();
            $table->string('document')->nullable();
            $table->string('image')->nullable();
            $table->string('date')->nullable();

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
        Schema::dropIfExists('inquiries');
    }
};
