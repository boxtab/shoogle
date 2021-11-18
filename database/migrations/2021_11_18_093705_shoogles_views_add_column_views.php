<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ShooglesViewsAddColumnViews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shoogles_views', function (Blueprint $table) {
            $table->unsignedBigInteger('views')->nullable()->after('last_view');
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
            $table->dropColumn('views');
        });
    }
}
