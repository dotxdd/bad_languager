<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('clickup_users', function (Blueprint $table) {
            $table->id();
            $table->string('clickup_user_id')->unique();
            $table->string('username');
            $table->string('email')->nullable();
            $table->unsignedBigInteger('user_id'); // Dodajemy kolumnę user_id
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Ustawiamy klucz obcy
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clickup_users');
    }
};
