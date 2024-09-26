<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::table('order_rooms', function (Blueprint $table) {
            // Change the customer_id from VARCHAR to BIGINT
            // $table->unsignedBigInteger('customer_id')->change();
            DB::statement('ALTER TABLE order_rooms ALTER COLUMN customer_id TYPE BIGINT USING customer_id::BIGINT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_rooms', function (Blueprint $table) {
            // Change the customer_id from VARCHAR to BIGINT
            // $table->string('customer_id')->change();
            DB::statement('ALTER TABLE order_rooms ALTER COLUMN customer_id TYPE VARCHAR(255)');
        });
    }
};
