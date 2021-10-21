<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NotificationToUserDetailColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications_to_user', function (Blueprint $table) {
            $table->unsignedBigInteger('shoogle_id')->nullable()->after('notification');
            $table->unsignedBigInteger('from_user_id')->nullable()->after('shoogle_id');
            $table->text('from_message')->nullable()->after('from_user_id');
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
            $table->dropColumn(['shoogle_id', 'from_user_id', 'from_message']);
        });
    }
}
