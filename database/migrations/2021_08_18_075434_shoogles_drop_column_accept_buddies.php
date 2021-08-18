<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ShooglesDropColumnAcceptBuddies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('shoogles', 'accept_buddies')) {
            Schema::table('shoogles', function (Blueprint $table) {
                $table->dropColumn('accept_buddies');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shoogles', function (Blueprint $table) {

            $table->boolean('accept_buddies')
                ->default(1)
                ->after('cover_image');

        });
    }
}
