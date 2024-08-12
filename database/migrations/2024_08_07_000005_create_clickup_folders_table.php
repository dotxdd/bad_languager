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
        Schema::create('clickup_folders', function (Blueprint $table) {
            $table->id();
            $table->string('clickup_folder_id')->unique();
            $table->string('name');
            $table->unsignedBigInteger('space_id');
            $table->foreign('space_id')->references('id')->on('clickup_spaces')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clickup_folders');
    }
};
