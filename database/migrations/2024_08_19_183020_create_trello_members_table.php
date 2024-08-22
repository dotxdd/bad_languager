<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrelloMembersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('trello_members', function (Blueprint $table) {
            $table->id();
            $table->string('trello_user_id')->unique();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->unsignedBigInteger('user_id'); // Dodajemy kolumnę user_id
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('trello_members');
    }
}
