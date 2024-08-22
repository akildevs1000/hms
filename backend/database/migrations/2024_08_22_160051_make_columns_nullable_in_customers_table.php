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
            $table->string("id_frontend_side")->nullable()->default(null)->change();
            $table->string("id_backend_side")->nullable()->default(null)->change();
            $table->string("captured_photo")->nullable()->default(null)->change();
            $table->string("sign")->nullable()->default(null)->change();
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
            $table->string("id_frontend_side")->nullable(false)->default('')->change();
            $table->string("id_backend_side")->nullable(false)->default('')->change();
            $table->string("captured_photo")->nullable(false)->default('')->change();
            $table->string("sign")->nullable(false)->default('')->change();
        });
    }
};
