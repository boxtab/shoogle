<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ShooglesChatId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shoogles', function (Blueprint $table) {
            $table->unsignedBigInteger('chat_id')->nullable()->after('views');
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
            $table->dropColumn('chat_id');
        });
    }
}
