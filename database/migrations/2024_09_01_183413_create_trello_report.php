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
        Schema::dropIfExists('trello_report_table');

        Schema::create('trello_report_table', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trello_card_id')->unique()->nullable();
            $table->unsignedBigInteger('trello_comment_id')->unique()->nullable();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('explict_message');
            $table->boolean('is_explict')->default(true);
            $table->timestamps();
            // Adding indexes for foreign key columns
            $table->index('trello_card_id');
            $table->index('trello_comment_id');

            $table->index('user_id');
            $table->foreign('trello_comment_id')->references('id')->on('trello_comments')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('trello_card_id')->references('id')->on('trello_cards')->onDelete('cascade');
        });
    }

    public function down()
    {

        Schema::dropIfExists('trello_report_table');
    }
};
