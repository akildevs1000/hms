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
        Schema::table('admin_expenses', function (Blueprint $table) {
            $table->string("notes")->nullable();
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('company_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admin_expenses', function (Blueprint $table) {
            $table->dropColumn("notes");
            $table->dropColumn('tax');
            $table->dropColumn('company_id');
        });
    }
};
