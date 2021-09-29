<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserHasShoogleCascadeDelete extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_has_shoogle', function (Blueprint $table) {

            $table->dropForeign('user_has_shoogle_shoogle_id_foreign');

            $table->foreign('shoogle_id')
                ->references('id')->on('shoogles')
                ->onDelete('cascade');
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

            $table->dropForeign('user_has_shoogle_shoogle_id_foreign');

            $table->foreign('shoogle_id')->references('id')->on('shoogles');
        });
    }
}
