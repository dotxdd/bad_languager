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
        Schema::create('clickup_teams', function (Blueprint $table) {
            $table->id();
            $table->string('clickup_team_id')->unique();
            $table->string('name');
            $table->timestamps();
            $table->unsignedBigInteger('user_id'); // Dodajemy kolumnę user_id
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Ustawiamy klucz obcy
        });
    }

    public function down()
    {
        Schema::dropIfExists('clickup_teams');
    }
};
