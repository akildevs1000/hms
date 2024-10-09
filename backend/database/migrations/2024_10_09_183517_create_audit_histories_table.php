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
        Schema::create('audit_histories', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->json('data')->nullable(); // JSON column to store data
            $table->unsignedBigInteger('company_id')->default(0);
            $table->string('dateTime')->nullable(); // Storing dateTime as string
            $table->timestamps(); // Laravel managed created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_histories');
    }
};
