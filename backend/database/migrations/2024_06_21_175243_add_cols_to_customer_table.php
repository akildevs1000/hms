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
        Schema::table('customers', function (Blueprint $table) {
            $table->string("id_frontend_side")->default();
            $table->string("id_backend_side")->default();
            $table->string("captured_photo")->default();
            $table->string("sign")->default();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn("id_frontend_side");
            $table->dropColumn("id_backend_side");
            $table->dropColumn("captured_photo");
            $table->dropColumn("sign");
        });
    }
};
