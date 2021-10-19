<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NotificationsTypeForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications_to_user', function (Blueprint $table) {
            $table->foreign('type_id')
                ->references('id')
                ->on('notifications_type')
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
            $table->dropForeign(['notifications_to_user_type_id']);
        });
    }
}
