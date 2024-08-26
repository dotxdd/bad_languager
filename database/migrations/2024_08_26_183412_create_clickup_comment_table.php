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
        Schema::create('clickup_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->text('comment');
            $table->string('clickup_comment_id');
            $table->timestamps();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('task_id')->references('id')->on('clickup_tasks')->onDelete('cascade');

            $table->foreign('user_id')->references('id')->on('clickup_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clickup_comments');
    }
};
