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
        Schema::create('audits', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->nullable();
            $table->integer('company_id')->nullable();
            $table->string('source')->nullable();
            $table->string('rev_no')->nullable();
            $table->string('rooms')->nullable();
            $table->date('checkin')->nullable();
            $table->date('checkout')->nullable();
            $table->decimal('tariff')->nullable();
            $table->decimal('advance')->nullable();
            $table->decimal('cash')->nullable();
            $table->decimal('card')->nullable();
            $table->decimal('online')->nullable();
            $table->decimal('bank')->nullable();
            $table->decimal('upi')->nullable();
            $table->decimal('balance')->nullable();
            $table->text('remark')->nullable();
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
        Schema::dropIfExists('audits');
    }
};
