<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ShooglesDropColumnReinder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shoogles', function (Blueprint $table) {
            $table->dropColumn('reminder');
            $table->dropColumn('reminder_interval');
            $table->dropColumn('is_reminder');
            $table->dropColumn('is_repetitive');
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
            $table->dateTime('reminder')->after('title');
            $table->string('reminder_interval', 1024)->nullable()->after('reminder');
            $table->boolean('is_reminder')->nullable()->after('reminder_interval');
            $table->boolean('is_repetitive')->nullable()->after('is_reminder');
        });
    }
}
