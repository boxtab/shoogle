<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BuddyRequestNotificationId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('buddy_request', function (Blueprint $table) {
            $table->unsignedBigInteger('notification_id')->nullable()->after('message');
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
            $table->dropColumn('notification_id');
        });
    }
}
