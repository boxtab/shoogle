<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserHasShoogleUnique extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_has_shoogle', function (Blueprint $table) {
            $table->unique(['user_id', 'shoogle_id'], 'user_has_shoogle_unique');
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
            $table->dropUnique('user_has_shoogle_unique');
        });
    }
}
