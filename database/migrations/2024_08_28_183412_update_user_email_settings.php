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
        Schema::table('users', function (Blueprint $table) {
         $table->boolean('is_downloaded_trello_mail')->default(1);
         $table->boolean('is_downloaded_clickup_mail')->default(1);
         $table->boolean('is_reportable_mail')->default(1);


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_downloaded_trello_mail');
            $table->dropColumn('is_downloaded_clickup_mail');
            $table->dropColumn('is_reportable_mail');


        });
    }
};
