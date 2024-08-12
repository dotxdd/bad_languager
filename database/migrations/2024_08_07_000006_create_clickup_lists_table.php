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
        Schema::create('clickup_lists', function (Blueprint $table) {
            $table->id();
            $table->string('clickup_list_id')->unique();
            $table->string('name');
            $table->unsignedBigInteger('folder_id');
            $table->foreign('folder_id')->references('id')->on('clickup_folders')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clickup_lists');
    }
};
