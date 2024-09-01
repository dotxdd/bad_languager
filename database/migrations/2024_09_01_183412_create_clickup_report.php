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
        Schema::dropIfExists('clickup_report_table');

        Schema::create('clickup_report_table', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clickup_task_id')->unique()->nullable();
            $table->unsignedBigInteger('clickup_comment_id')->unique()->nullable();
            $table->unsignedBigInteger('assignee_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('explict_message');
            $table->boolean('is_explict')->default(true);
            $table->timestamps();
            // Adding indexes for foreign key columns
            $table->index('clickup_task_id');
            $table->index('clickup_comment_id');
            $table->index('assignee_id');
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('clickup_task_id')->references('id')->on('clickup_tasks')->onDelete('cascade');
            $table->foreign('assignee_id')->references('id')->on('clickup_users')->onDelete('cascade');
            $table->foreign('clickup_comment_id')->references('id')->on('clickup_comments')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('clickup_report_table', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['user_id']);
            $table->dropForeign(['clickup_task_id']);
            $table->dropForeign(['assignee_id']);
            $table->dropForeign(['clickup_comment_id']);


            // Drop indexes
            $table->dropIndex(['user_id']);
            $table->dropIndex(['clickup_task_id']);
            $table->dropIndex(['assignee_id']);
            $table->dropIndex(['clickup_comment_id']);

        });

        // Drop the table
        Schema::dropIfExists('clickup_report_table');
    }
};
