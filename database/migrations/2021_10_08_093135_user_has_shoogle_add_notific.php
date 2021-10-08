<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserHasShoogleAddNotific extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_has_shoogle', function (Blueprint $table) {
            $table->timestamp('last_notification')->nullable()->after('is_reminder');
            $table->timestamp('in_process')->nullable()->after('last_notification');
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
            $table->dropColumn('last_notification');
            $table->dropColumn('in_process');
        });
    }
}
