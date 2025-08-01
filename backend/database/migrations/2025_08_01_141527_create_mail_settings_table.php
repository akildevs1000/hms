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
        Schema::create('mail_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->unique();
            $table->string('mailer')->default('smtp');
            $table->string('host')->default('smtp.gmail.com');
            $table->integer('port')->default(587);
            $table->string('username')->default('user@example.com');
            $table->string('password')->default('secret');
            $table->string('encryption')->default('tls')->nullable();
            $table->string('from_address')->default('noreply@example.com');
            $table->string('from_name')->default('Company Name');
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
        Schema::dropIfExists('mail_settings');
    }
};
