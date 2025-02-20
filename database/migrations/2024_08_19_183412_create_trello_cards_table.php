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
        Schema::create('trello_cards', function (Blueprint $table) {
            $table->id();
            $table->string('trello_id')->unique();
            $table->unsignedBigInteger('board_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('trello_members')->onDelete('cascade');

            $table->foreign('board_id')->references('id')->on('trello_boards')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trello_cards');
    }
};
