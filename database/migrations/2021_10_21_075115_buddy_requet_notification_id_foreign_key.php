<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BuddyRequetNotificationIdForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('buddy_request', function (Blueprint $table) {
            $table->foreign('notification_id')
                ->references('id')
                ->on('notifications_to_user')
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
        Schema::table('buddy_request', function (Blueprint $table) {
            $table->dropForeign(['buddy_request_notification_id_foreign']);
        });
    }
}
