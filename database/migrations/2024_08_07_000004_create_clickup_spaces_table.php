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
        Schema::create('clickup_spaces', function (Blueprint $table) {
            $table->id();
            $table->string('clickup_space_id')->unique();
            $table->string('name');
            $table->unsignedBigInteger('team_id');
            $table->foreign('team_id')->references('id')->on('clickup_teams')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clickup_spaces');
    }
};
