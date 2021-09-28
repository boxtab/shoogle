<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ShoogleChatIdString extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shoogles', function (Blueprint $table) {
            $table->string('chat_id', 1024)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shoogles', function (Blueprint $table) {
            $table->unsignedBigInteger('chat_id')->change();
        });
    }
}
