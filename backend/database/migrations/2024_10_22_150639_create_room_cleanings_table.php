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
        Schema::create('room_cleanings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("room_id")->default(0);
            $table->string("status")->default("Dirty"); // Dirty or Cleaned
            $table->time('start_time')->default('00:00');
            $table->time('end_time')->default('00:00');
            $table->time('total_time')->default('00:00');
            $table->string('before_attachment')->nullable();
            $table->string('after_attachment')->nullable();
            $table->string('voice_note')->nullable();
            $table->unsignedBigInteger("cleaned_by_user_id")->default(0);
            $table->unsignedBigInteger("response_by_user_id")->default(0);
            $table->unsignedBigInteger("company_id")->default(0);
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
        Schema::dropIfExists('room_cleanings');
    }
};
