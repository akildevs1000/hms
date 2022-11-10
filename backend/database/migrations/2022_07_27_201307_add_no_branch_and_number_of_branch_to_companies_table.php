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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('company_code')->comment('AE0001');
            $table->boolean('no_branch')->default(0);
            $table->string('max_branches')->default(0);

            $table->string('lat')->nullable();
            $table->string('lon')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('no_branch');
            $table->dropColumn('max_branches');
            $table->dropColumn('company_code');
            $table->dropColumn('lat');
            $table->dropColumn('lon');
        });
    }
};
