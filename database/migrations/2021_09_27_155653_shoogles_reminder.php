<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ShooglesReminder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shoogles', function (Blueprint $table) {
            $table->boolean('is_reminder')->nullable()->default(false)->after('reminder_interval');
            $table->boolean('is_repetitive')->nullable()->default(false)->after('is_reminder');
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
            $table->dropColumn('is_reminder');
            $table->dropColumn('is_repetitive');
        });
    }
}
