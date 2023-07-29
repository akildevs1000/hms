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
        Schema::create('lost_and_found_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->integer('booking_id');
            $table->string('item_name');
            $table->datetime('missing_datetime');
            $table->string('missing_remarks')->nullable();
            $table->string('missing_notes')->nullable();

            $table->datetime('found_datetime')->nullable();
            $table->string('found_person_name')->nullable();
            $table->string('found_location')->nullable();
            $table->string('found_remarks')->nullable();
            $table->string('found_notes')->nullable();

            $table->datetime('returned_datetime')->nullable();
            $table->string('returned_person_name')->nullable();
            $table->string('returned_remarks')->nullable();
            $table->string('returned_notes')->nullable();
            $table->string('approved_person_name')->nullable();
            $table->integer('status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lost_and_found_items');
    }
};
