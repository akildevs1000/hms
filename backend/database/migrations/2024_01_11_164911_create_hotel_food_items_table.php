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
        Schema::create('hotel_food_items', function (Blueprint $table) {
            $table->id();
            $table->integer("company_id");
            $table->string("name");
            $table->integer("category_id");
            $table->integer("timing_id");
            $table->string("ingredients")->nullable();
            $table->integer("is_non_veg")->default(0);
            $table->text("description")->nullable();
            $table->decimal('amount', 8, 2)->default(0);
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('hotel_food_items');
    }
};
