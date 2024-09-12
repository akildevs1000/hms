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
        Schema::table('users', function (Blueprint $table) {
            $table->string('telegram_chat_id')->nullable();
            $table->string('telegram_otp')->nullable();
            $table->timestamp('telegram_otp_expires_at')->nullable(); // To store expiration timestamp
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('telegram_chat_id');
            $table->dropColumn('telegram_otp');
            $table->dropColumn('telegram_otp_expires_at');
        });
    }
};
