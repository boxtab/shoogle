<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ShoogleChatId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_has_shoogle', function (Blueprint $table) {
            $table->string('chat_id', 1024)->nullable()->after('is_reminder');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_has_shoogle', function (Blueprint $table) {
            $table->dropColumn('chat_id');
        });
    }
}
