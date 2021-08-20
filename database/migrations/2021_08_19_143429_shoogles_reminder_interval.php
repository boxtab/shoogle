<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ShooglesReminderInterval extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shoogles', function (Blueprint $table) {
            $table->string('reminder_interval', 1024)->nullable()->after('reminder');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shoogles', function (Blueprint $table) {
            $table->dropColumn('reminder_interval');
        });
    }
}
