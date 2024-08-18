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
        Schema::create('clickup_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('clickup_task_id')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status');
            $table->unsignedBigInteger('list_id');
            $table->unsignedBigInteger('assignee_id')->nullable();
            $table->unsignedBigInteger('creator_id');
            $table->timestamps();

            // Adding indexes for foreign key columns
            $table->index('list_id');
            $table->index('assignee_id');
            $table->index('creator_id');

            // Foreign key constraints
            $table->foreign('list_id')->references('id')->on('clickup_lists')->onDelete('cascade');
            $table->foreign('assignee_id')->references('id')->on('clickup_users')->onDelete('set null');
            $table->foreign('creator_id')->references('id')->on('clickup_users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('clickup_tasks', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['list_id']);
            $table->dropForeign(['assignee_id']);
            $table->dropForeign(['creator_id']);

            // Drop indexes
            $table->dropIndex(['list_id']);
            $table->dropIndex(['assignee_id']);
            $table->dropIndex(['creator_id']);
        });

        // Drop the table
        Schema::dropIfExists('clickup_tasks');
    }
};
