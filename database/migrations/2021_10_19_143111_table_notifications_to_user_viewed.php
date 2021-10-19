<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableNotificationsToUserViewed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications_to_user', function (Blueprint $table) {
            $table->boolean('viewed')->after('type_id')->default(0);
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
            $table->dropColumn('viewed');
        });
    }
}
