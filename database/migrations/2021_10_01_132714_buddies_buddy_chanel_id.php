<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BuddiesBuddyChanelId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('buddies', function (Blueprint $table) {
            $table->string('chat_id', 1024)->nullable()->after('disconnected_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('buddies', function (Blueprint $table) {
            $table->dropColumn('chat_id');
        });
    }
}
