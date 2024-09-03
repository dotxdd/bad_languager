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
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('explict_message');
            $table->boolean('is_explict')->default(true);
            $table->timestamps();
            // Adding indexes for foreign key columns
            $table->index('trello_card_id');
            $table->index('assignee_id');
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('trello_card_id')->references('id')->on('trello_cards')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('trello_report_table', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['user_id']);
            $table->dropForeign(['trello_card_id']);


            // Drop indexes
            $table->dropIndex(['user_id']);
            $table->dropIndex(['trello_card_id']);

        });

        // Drop the table
        Schema::dropIfExists('trello_report_table');
    }
};
