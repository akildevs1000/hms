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
        Schema::create('food_prices', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->decimal('breakfast', 8, 2)->default(0);
            $table->decimal('lunch', 8, 2)->default(0);
            $table->decimal('dinner', 8, 2)->default(0);
            $table->integer('company_id');
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
        Schema::dropIfExists('food_prices');
    }
};
