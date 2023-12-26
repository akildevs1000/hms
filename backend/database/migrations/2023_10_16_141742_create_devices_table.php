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
        Schema::dropIfExists('devices');
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string("serial_number");
            $table->integer("company_id");
            $table->integer("room_id");
            $table->string("name");
            $table->integer("latest_status")->nullable();
            $table->datetime("latest_status_time")->nullable();
            $table->integer("user_id")->nullable();
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
        Schema::dropIfExists('devices');
    }
};
