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
        Schema::table('inquiries', function (Blueprint $table) {

            $table->unsignedBigInteger("business_source_id")->default(0);
            $table->unsignedBigInteger("source_id")->default(0);
            $table->unsignedBigInteger("room_type_id")->default(0);

            $table->string("source_type")->nullable();
            $table->string("source_name")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropColumn("business_source_id");
            $table->dropColumn("source_id");
            $table->dropColumn("room_type_id");

            $table->dropColumn("source_type");
            $table->dropColumn("source_name");
        });
    }
};
