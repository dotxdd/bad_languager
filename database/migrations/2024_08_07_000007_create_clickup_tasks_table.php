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
            $table->unsignedBigInteger('workspace_id');
            $table->unsignedBigInteger('assignee_id')->nullable();
            $table->unsignedBigInteger('creator_id');
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();

            $table->foreign('list_id')->references('id')->on('clickup_lists')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('clickup_tasks');
    }
};
