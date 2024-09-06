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
        Schema::table('quotations', function (Blueprint $table) {
            $table->string("type")->nullable();
            $table->decimal('extra', 10, 2)->default(0);
            $table->string("discount_reason")->nullable();
            $table->string("extra_reason")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn("type");
            $table->dropColumn("extra");
            $table->dropColumn("discount_reason");
            $table->dropColumn("extra_reason");
        });
    }
};
