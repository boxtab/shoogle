<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NotificationsToUserForeignKeyForDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications_to_user', function (Blueprint $table) {

            $table->foreign('shoogle_id')
                ->references('id')
                ->on('shoogles')
                ->onUpdate('no action')
                ->onDelete('no action');

            $table->foreign('from_user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('no action')
                ->onDelete('no action');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications_to_user', function (Blueprint $table) {
            $table->dropForeign('notifications_to_user_shoogle_id_foreign');
            $table->dropForeign('notifications_to_user_from_user_id_foreign');
        });
    }
}
