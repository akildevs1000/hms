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
        Schema::create('admin_expense_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("admin_expense_id")->default(0);
            $table->string("attachment");
            $table->string("slug");
            $table->string("model");
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
        Schema::dropIfExists('admin_expense_attachments');
    }
};
