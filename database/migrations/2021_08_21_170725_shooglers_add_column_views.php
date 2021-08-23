<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ShooglersAddColumnViews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shoogles', function (Blueprint $table) {
            if ( ! Schema::hasColumn('shoogles', 'views') ) {
                $table->unsignedBigInteger('views')->default(0)->after('cover_image');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('shoogles', 'views')) {
            Schema::table('shoogles', function (Blueprint $table) {
                $table->dropColumn('views');
            });
        }
    }
}
