<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ShooglesViewsUniqueKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shoogles_views', function (Blueprint $table) {
            $table->unique(['shoogle_id', 'user_id'], 'shoogles_views_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shoogles_views', function (Blueprint $table) {
            $table->dropUnique('shoogles_views_unique');
        });
    }
}
