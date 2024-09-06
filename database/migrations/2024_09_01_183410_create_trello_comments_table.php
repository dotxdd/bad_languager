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
        Schema::create('trello_comments', function (Blueprint $table) {
            $table->id();
            $table->string('trello_comment_id')->unique();
            $table->unsignedBigInteger('card_id');
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('trello_members')->onDelete('cascade');

            $table->foreign('card_id')->references('id')->on('trello_cards')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trello_comments');
    }
};
