<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NotificationToUserBuddyIdBuddyRequestId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications_to_user', function (Blueprint $table) {
            $table->unsignedBigInteger('buddy_request_id')->nullable()->after('from_message');
            $table->unsignedBigInteger('buddy_id')->nullable()->after('buddy_request_id');

            $table->foreign('buddy_request_id', 'notifications_to_user_buddy_request_id_foreign')
                ->references('id')
                ->on('buddy_request');

            $table->foreign('buddy_id', 'buddies_buddy_id_foreign')
                ->references('id')
                ->on('buddies');
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

            if (Schema::hasColumn('notifications_to_user', 'buddy_request_id')) {
                $table->dropColumn('buddy_request_id');
            }

            if (Schema::hasColumn('notifications_to_user', 'buddy_id')) {
                $table->dropColumn('buddy_id');
            }

        });
    }
}
