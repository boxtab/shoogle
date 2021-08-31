<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BuddyRequestUnique extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('buddy_request', function (Blueprint $table) {
            $table->unique(['shoogle_id', 'user1_id', 'user2_id'], 'buddy_request_unique');
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
            $table->dropUnique('buddy_request_unique');
        });
    }
}
