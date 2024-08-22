<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trello_boards', function (Blueprint $table) {
            $table->id();
            $table->string('board_id')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('user_id'); // Dodajemy kolumnę user_id
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trello_boards');
    }
};
